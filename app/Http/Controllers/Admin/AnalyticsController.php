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
            return redirect()->route('admin.analytics.index')
                ->with('success', 'Python Analytics Report generated successfully via subprocess & OS module!');
        }

        return redirect()->route('admin.analytics.index')
            ->with('error', 'Failed to generate Python report: ' . ($result['message'] ?? 'Unknown error'));
    }

    /**
     * View generated Python report content.
     */
    public function viewReport()
    {
        $reportPath = storage_path('app/python_reports/waste_report.txt');

        if (!file_exists($reportPath)) {
            return redirect()->route('admin.analytics.index')
                ->with('error', 'Report file does not exist yet. Please generate the Python report first.');
        }

        $content = file_get_contents($reportPath);

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="waste_report.txt"',
        ]);
    }
}
