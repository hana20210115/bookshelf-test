<?php

namespace App\Http\Controllers;

use App\Services\RankingService;
use Illuminate\View\View;

class RankingController extends Controller
{
    private RankingService $rankingService;

    public function __construct(RankingService $rankingService)
    {
        $this->rankingService = $rankingService;
    }

    /**
     * ランキング画面を表示
     *
     * @return View;
     */
    public function index(): View
    {
        $rankedBooks = $this->rankingService->getTopRatedBooks();

        return view('ranking.index', compact('rankedBooks'));
    }
}
