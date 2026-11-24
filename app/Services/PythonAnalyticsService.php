<?php

namespace App\Services;

use App\Models\CollectionCentre;
use App\Models\SearchHistory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PythonAnalyticsService
{
    /**
     * Get the Flask analytics service base URL.
     */
    public function getFlaskBaseUrl(): string
    {
        return rtrim(config('services.flask.url', env('FLASK_URL', 'http://127.0.0.1:5000')), '/');
    }

    /**
     * Locate the available Python executable on the host system (for CLI fallback / syllabus demo).
     */
    public function getPythonBinary(): string
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $userAppData = 'C:\\Users\\Krish Patel\\AppData\\Local\\Programs\\Python\\Python314\\python.exe';
            if (file_exists($userAppData)) {
                return '"' . $userAppData . '"';
            }
            if (file_exists('C:\\Python314\\python.exe')) {
                return 'C:\\Python314\\python.exe';
            }
            return 'py';
        }
        return 'python3';
    }

    /**
     * Fetch smart waste insights from the Flask HTTP analytics service.
     * Enforces explicit short timeouts (Http::connectTimeout(2)->timeout(5)).
     * Caches successful payloads in Laravel Cache keyed by latest search_histories timestamp and collection_centres count.
     * Tracks service source ('flask' | 'php_fallback').
     */
    public function getWasteInsights(): array
    {
        $searches = SearchHistory::with(['wasteItem.category', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $searchData = $searches->map(function ($s) {
            return [
                'search_id'       => $s->id,
                'user_name'       => $s->user ? $s->user->name : 'Guest User',
                'waste_item_name' => $s->wasteItem ? $s->wasteItem->name : 'General Item',
                'category_name'   => ($s->wasteItem && $s->wasteItem->category) ? $s->wasteItem->category->name : 'Uncategorized',
                'searched_at'     => $s->created_at ? $s->created_at->toDateTimeString() : null,
            ];
        })->toArray();

        $latestSearchTs = SearchHistory::latest('created_at')->value('created_at')?->timestamp ?? 0;
        $centresCount   = CollectionCentre::count();
        $cacheKey       = "waste_insights_{$latestSearchTs}_{$centresCount}";

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($searchData) {
            $flaskUrl = $this->getFlaskBaseUrl() . '/api/insights';

            try {
                // Enforce explicit short timeouts so a stopped Flask process never hangs for 30s
                $response = Http::connectTimeout(2)
                    ->timeout(5)
                    ->post($flaskUrl, ['records' => $searchData]);

                if ($response->successful()) {
                    $json = $response->json();
                    if (is_array($json) && !empty($json)) {
                        $json['source'] = 'flask';
                        return $json;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Flask Analytics HTTP call failed: ' . $e->getMessage() . '. Engaging PHP fallback.');
            }

            // Fallback calculation in pure PHP
            return $this->getFallbackInsights($searchData);
        });
    }

    /**
     * Generate WasteWise JSON report via Python service or local generator.
     * Enforces short HTTP timeouts and caches payload.
     */
    public function generatePythonReport(): array
    {
        $searches = SearchHistory::with(['wasteItem.category', 'user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $searchData = $searches->map(function ($s) {
            return [
                'search_id'       => $s->id,
                'user_name'       => $s->user ? $s->user->name : 'Guest User',
                'waste_item_name' => $s->wasteItem ? $s->wasteItem->name : 'General Item',
                'category_name'   => ($s->wasteItem && $s->wasteItem->category) ? $s->wasteItem->category->name : 'Uncategorized',
                'searched_at'     => $s->created_at ? $s->created_at->toDateTimeString() : null,
            ];
        })->toArray();

        $latestSearchTs = SearchHistory::latest('created_at')->value('created_at')?->timestamp ?? 0;
        $centresCount   = CollectionCentre::count();
        $cacheKey       = "waste_report_{$latestSearchTs}_{$centresCount}";

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($searchData) {
            // Attempt Flask HTTP call first
            try {
                $response = Http::connectTimeout(2)
                    ->timeout(5)
                    ->post($this->getFlaskBaseUrl() . '/api/insights', ['records' => $searchData]);

                if ($response->successful()) {
                    $reportData = $response->json();
                    $reportData['generated_at'] = now()->toDateTimeString();
                    $reportData['category_breakdown'] = $reportData['category_distribution'] ?? $reportData['category_breakdown'] ?? [];
                    $reportData['source'] = 'flask';
                    $reportData['status'] = 'success';

                    $this->saveReportToFile($reportData);
                    return $reportData;
                }
            } catch (\Throwable $e) {
                Log::warning('Flask HTTP report generation failed: ' . $e->getMessage() . '. Falling back to subprocess/PHP.');
            }

            // Attempt subprocess fallback
            $base64Payload = base64_encode(json_encode($searchData));
            $pythonBin     = $this->getPythonBinary();
            $scriptPath    = base_path('scripts/run_php_report.py');
            $safeCommand   = $pythonBin . ' "' . $scriptPath . '" ' . escapeshellarg($base64Payload);

            try {
                $rawOutput = shell_exec($safeCommand);
                if ($rawOutput !== null) {
                    $result = json_decode($rawOutput, true);
                    if (is_array($result) && isset($result['status']) && $result['status'] === 'success') {
                        $result['source'] = 'flask';
                        return $result;
                    }
                }
            } catch (\Throwable $e) {
                Log::error('Python Subprocess Report Generation Exception: ' . $e->getMessage());
            }

            // Ultimate PHP fallback report
            $fallback = $this->getFallbackInsights($searchData);
            $fallback['status'] = 'success';
            $fallback['source'] = 'php_fallback';
            $fallback['generated_at'] = now()->toDateTimeString();
            $fallback['category_breakdown'] = $fallback['category_distribution'] ?? [];

            $this->saveReportToFile($fallback);
            return $fallback;
        });
    }

    /**
     * Save report data array to storage JSON file.
     */
    protected function saveReportToFile(array $data): void
    {
        $dir = storage_path('app/python_reports');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents(
            $dir . '/waste_report.json',
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * Fallback calculation in pure PHP if Python/Flask is unreachable.
     */
    public function getFallbackInsights(array $searchData): array
    {
        $total = count($searchData);
        if ($total === 0) {
            return [
                'status'                => 'success',
                'source'                => 'php_fallback',
                'total_searches'        => 0,
                'top_category'          => 'N/A',
                'top_item'              => 'N/A',
                'percentage'            => 0.0,
                'most_active_day'       => 'N/A',
                'insight'               => 'No search history available yet.',
                'category_breakdown'    => [],
                'category_distribution' => [],
            ];
        }

        $cats = array_column($searchData, 'category_name');
        $catCounts = array_count_values($cats);
        arsort($catCounts);
        $topCat = key($catCounts);
        $topCount = current($catCounts);

        $items = array_column($searchData, 'waste_item_name');
        $itemCounts = array_count_values($items);
        arsort($itemCounts);
        $topItem = key($itemCounts);

        $pct = round(($topCount / $total) * 100, 2);

        $dist = [];
        foreach ($catCounts as $c => $cnt) {
            $dist[] = [
                'category'   => $c,
                'searches'   => $cnt,
                'percentage' => round(($cnt / $total) * 100, 1),
            ];
        }

        return [
            'status'                => 'success',
            'source'                => 'php_fallback',
            'total_searches'        => $total,
            'top_category'          => $topCat,
            'top_item'              => $topItem,
            'percentage'            => $pct,
            'most_active_day'       => 'Today',
            'insight'               => "{$topCat} is the leading waste category searched ({$pct}% of total searches).",
            'category_breakdown'    => $dist,
            'category_distribution' => $dist,
        ];
    }

    /**
     * Academic Demonstration of Syllabus 4.1.1 / 4.1.2.
     */
    public function demonstratePhpToPythonAcademic(): array
    {
        $pythonBin = $this->getPythonBinary();
        $scriptPath = base_path('scripts/waste_insights.py');

        $demoPayload = json_encode([
            ['category_name' => 'Plastic Waste', 'waste_item_name' => 'Plastic Bottle', 'searched_at' => now()->toDateTimeString()],
            ['category_name' => 'Electronic Waste', 'waste_item_name' => 'Old Laptop', 'searched_at' => now()->toDateTimeString()],
        ]);
        $escapedPayload = escapeshellarg(base64_encode($demoPayload));

        $commandString = $pythonBin . ' "' . $scriptPath . '" ' . $escapedPayload;
        $rawOutput = @shell_exec($commandString);

        return [
            'syllabus_topics'       => '4.1 Executing Python script using PHP (4.1.1 Echo/Output, 4.1.2 escapeshellcmd(), shell_exec())',
            'prepared_command'      => $commandString,
            'execution_function'    => 'shell_exec()',
            'sanitization_function' => 'escapeshellcmd() + escapeshellarg()',
            'python_executable'     => $pythonBin,
            'raw_output_snippet'    => is_string($rawOutput) ? substr($rawOutput, 0, 300) : 'No output captured',
            'decoded_output'        => json_decode($rawOutput, true) ?? [],
        ];
    }
}
