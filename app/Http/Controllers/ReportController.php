<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Services\ReportService;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * @var ReportService;
     */
    protected $reportService;

    /**
     * コンストラクタ
     * 
     * @param ReportService $reportService
     */
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * マイ読書レポート画面を表示する
     * 
     * @return View
     */
    public function index():View
    {
        $userId = Auth::id();

        $summary = $this->reportService->getSummary($userId);

        $topRatedBooks = $this->reportService->getTopRatedBooks($userId);

        $topGenres = $this->reportService->getTopGenres($userId);

        return view('reports.index',compact('summary','topRatedBooks','topGenres'));
    }
}
