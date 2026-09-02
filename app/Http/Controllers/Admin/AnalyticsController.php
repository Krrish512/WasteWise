<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PythonAnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    protected PythonAnalyticsService $analyticsService;

    public function __construct(PythonAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display the Smart Waste Analytics page with Python insights.
     */
    public function index()
    {
        $insights = $this->analyticsService->getWasteInsights();
        $academicDemo = $this->analyticsService->demonstratePhpToPythonAcademic();

        $reportPath = storage_path('app/python_reports/waste_report.json');
        $reportExists = file_exists($reportPath);
        $reportLastModified = $reportExists ? date('Y-m-d H:i:s', filemtime($reportPath)) : null;

        return view('admin.analytics.index', compact(
            'insights',
            'academicDemo',
            'reportExists',
            'reportLastModified'
        ));
    }

    /**
     * Trigger Python report generation via subprocess.
     */
    public function generateReport()
    {
        $result = $this->analyticsService->generatePythonReport();

        if (isset($result['status']) && $result['status'] === 'success') {
            return redirect()->route('admin.analytics.view-report')
                ->with('success', 'WasteWise JSON Analytics Report generated successfully via Python!');
        }

        return redirect()->route('admin.analytics.index')
            ->with('error', 'Failed to generate Python report: ' . ($result['message'] ?? 'Unknown error'));
    }

    /**
     * View generated Python report as formatted JSON.
     */
    public function viewReport()
    {
        $insights = $this->analyticsService->getWasteInsights();
        $reportPath = storage_path('app/python_reports/waste_report.json');
        $reportExists = file_exists($reportPath);
        $reportLastModified = $reportExists ? date('Y-m-d H:i:s', filemtime($reportPath)) : null;
        $reportSize = $reportExists ? filesize($reportPath) : 0;
        
        $reportJsonString = null;
        if ($reportExists) {
            $rawContent = file_get_contents($reportPath);
            $decoded = json_decode($rawContent, true);
            // Ensure clean pretty printing
            $reportJsonString = is_array($decoded) ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $rawContent;
        }

        return view('admin.analytics.report', compact(
            'insights',
            'reportExists',
            'reportLastModified',
            'reportSize',
            'reportJsonString'
        ));
    }

    /**
     * Download the raw generated JSON report file.
     */
    public function downloadReport()
    {
        $reportPath = storage_path('app/python_reports/waste_report.json');

        if (!file_exists($reportPath)) {
            return redirect()->route('admin.analytics.view-report')
                ->with('error', 'Report file does not exist yet. Please generate a report first.');
        }

        return response()->download($reportPath, 'waste_report.json', [
            'Content-Type' => 'application/json; charset=UTF-8',
        ]);
    }
}
