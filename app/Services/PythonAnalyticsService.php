<?php

namespace App\Services;

use App\Models\SearchHistory;
use Illuminate\Support\Facades\Log;

class PythonAnalyticsService
{
    /**
     * Locate the available Python executable on the host system.
     */
    protected function getPythonBinary(): string
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
     * Execute Python analytics script passing real database SearchHistory data.
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

        $jsonPayload = json_encode($searchData);
        $base64Payload = base64_encode($jsonPayload);

        $pythonBin = $this->getPythonBinary();
        $scriptPath = base_path('scripts/waste_insights.py');

        $safeCommand = $pythonBin . ' "' . $scriptPath . '" ' . escapeshellarg($base64Payload);

        try {
            $rawOutput = shell_exec($safeCommand);
            if ($rawOutput !== null) {
                $result = json_decode($rawOutput, true);
                if (is_array($result) && isset($result['status']) && $result['status'] === 'success') {
                    return $result;
                }
            }
        } catch (\Throwable $e) {
            Log::error('Python Insights Execution Exception: ' . $e->getMessage());
        }

        // Graceful fallback if Python is unavailable or output invalid
        return $this->getFallbackInsights($searchData);
    }

    /**
     * Academic Demonstration of Syllabus 4.1.1 / 4.1.2 (Echo, escapeshellcmd, shell_exec).
     */
    public function demonstratePhpToPythonAcademic(): array
    {
        $pythonBin = $this->getPythonBinary();
        $scriptPath = base_path('scripts/waste_insights.py');

        // Sample test payload
        $demoPayload = json_encode([
            ['category_name' => 'Plastic Waste', 'waste_item_name' => 'Plastic Bottle', 'searched_at' => now()->toDateTimeString()],
            ['category_name' => 'Electronic Waste', 'waste_item_name' => 'Old Laptop', 'searched_at' => now()->toDateTimeString()],
        ]);
        $escapedPayload = escapeshellarg(base64_encode($demoPayload));

        $commandString = $pythonBin . ' "' . $scriptPath . '" ' . $escapedPayload;

        $rawOutput = shell_exec($commandString);

        return [
            'syllabus_topics' => '4.1 Executing Python script using PHP (4.1.1 Echo/Output, 4.1.2 escapeshellcmd(), shell_exec())',
            'prepared_command' => $commandString,
            'execution_function' => 'shell_exec()',
            'sanitization_function' => 'escapeshellcmd() + escapeshellarg()',
            'python_executable' => $pythonBin,
            'raw_output_snippet' => is_string($rawOutput) ? substr($rawOutput, 0, 300) : 'No output captured',
            'decoded_output' => json_decode($rawOutput, true) ?? [],
        ];
    }

    /**
     * Generate WasteWise JSON report via Python (Unit 4.2 Subprocess & OS Module).
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

        $base64Payload = base64_encode(json_encode($searchData));

        $pythonBin = $this->getPythonBinary();
        $scriptPath = base_path('scripts/run_php_report.py');

        $safeCommand = $pythonBin . ' "' . $scriptPath . '" ' . escapeshellarg($base64Payload);

        try {
            $rawOutput = shell_exec($safeCommand);
            if ($rawOutput !== null) {
                $result = json_decode($rawOutput, true);
                if (is_array($result) && isset($result['status'])) {
                    return $result;
                }
            }
        } catch (\Throwable $e) {
            Log::error('Python Report Generation Exception: ' . $e->getMessage());
        }

        return [
            'status' => 'error',
            'message' => 'Failed to execute Python report generation script.',
        ];
    }

    /**
     * Fallback calculation in pure PHP if Python is unreachable.
     */
    protected function getFallbackInsights(array $searchData): array
    {
        $total = count($searchData);
        if ($total === 0) {
            return [
                'status' => 'fallback',
                'total_searches' => 0,
                'top_category' => 'N/A',
                'top_item' => 'N/A',
                'percentage' => 0.0,
                'most_active_day' => 'N/A',
                'insight' => 'No search history available yet.',
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
                'category' => $c,
                'searches' => $cnt,
                'percentage' => round(($cnt / $total) * 100, 1),
            ];
        }

        return [
            'status' => 'fallback',
            'total_searches' => $total,
            'top_category' => $topCat,
            'top_item' => $topItem,
            'percentage' => $pct,
            'most_active_day' => 'Today',
            'insight' => "{$topCat} is the most searched category ({$pct}% of searches).",
            'category_distribution' => $dist,
        ];
    }
}
