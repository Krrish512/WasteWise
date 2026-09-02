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

        $reportPath = storage_path('app/python_reports/waste_report.txt');
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
                ->with('success', 'Smart Waste Analytics Report generated successfully using Python integration!');
        }

        return redirect()->route('admin.analytics.index')
            ->with('error', 'Failed to generate Python report: ' . ($result['message'] ?? 'Unknown error'));
    }

    /**
     * View generated Python report styled in HTML.
     */
    public function viewReport()
    {
        $insights = $this->analyticsService->getWasteInsights();
        $reportPath = storage_path('app/python_reports/waste_report.txt');
        $reportExists = file_exists($reportPath);
        $reportLastModified = $reportExists ? date('Y-m-d H:i:s', filemtime($reportPath)) : null;
        $reportSize = $reportExists ? filesize($reportPath) : 0;
        $reportContent = $reportExists ? file_get_contents($reportPath) : null;

        return view('admin.analytics.report', compact(
            'insights',
            'reportExists',
            'reportLastModified',
            'reportSize',
            'reportContent'
        ));
    }

    /**
     * Download the raw generated text report file.
     */
    public function downloadReport()
    {
        $reportPath = storage_path('app/python_reports/waste_report.txt');

        if (!file_exists($reportPath)) {
            return redirect()->route('admin.analytics.view-report')
                ->with('error', 'Report file does not exist yet. Please generate a report first.');
        }

        return response()->download($reportPath, 'WasteWise_Analytics_Report.txt', [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
