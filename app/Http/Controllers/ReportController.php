<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * @var ReportService;
     */
    protected $reportService;

    /**
     * コンストラクタ
     */
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * マイ読書レポート画面を表示する
     */
    public function index(): View
    {
        $userId = Auth::id();

        $summary = $this->reportService->getSummary($userId);
        $topRatedBooks = $this->reportService->getTopRatedBooks($userId);
        $topGenres = $this->reportService->getTopGenres($userId);
        $ratingDistribution = $this->reportService->getRatingDistribution($userId);

        $stats = [
            'summary' => [
                'total_reviews' => $summary['total_reviews'],
                'average_rating' => $summary['average_rating'],
                'books_read' => $summary['completed_books_count'],
            ],
            'rating_distribution' => $ratingDistribution,
            'top_rated_books' => $topRatedBooks,
            'genre_ratings' => $topGenres,
        ];

        return view('reports.index', compact('stats'));
    }
}
