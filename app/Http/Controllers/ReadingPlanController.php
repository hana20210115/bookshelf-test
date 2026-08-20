<?php

namespace App\Http\Controllers;


use App\Models\ReadingPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ReadingPlan\StoreReadingPlanRequest;
use App\Http\Requests\ReadingPlan\UpdateReadingPlanRequest;
use App\Services\ReadingPlanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReadingPlanController extends Controller
{
    /**
     * @var ReadingPlanService
     */
    protected $readingPlanService;

    /**
     * コンストラクタ
     * @param ReadingPlanService $readingPlanService
     */
    public function __construct(ReadingPlanService $readingPlanService)
    {
        $this->readingPlanService = $readingPlanService;
    }

    /**
     * 読書計画一覧画面を表示する
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $readingPlans = $this->readingPlanService->getReadingPlans(
            Auth::id(),
            $request->status
        );

        return view('reading_plans.index', compact('readingPlans'));
    }

    /**
     * 読書計画の新規作成画面を表示する
     *
     * @return View
     */
    public function create(): View
    {
        $books = $this->readingPlanService->getAllBooks();

        return view('reading_plans.create', compact('books'));
    }

    /**
     * 読書計画を保存する
     *
     * @param StoreReadingPlanRequest $request
     * @return RedirectResponse
     */
    public function store(StoreReadingPlanRequest $request): RedirectResponse
    {
        $this->readingPlanService->createReadingPlan(Auth::id(), $request->validated());


        return redirect()->route('reading-plans.create')->with('success', '読書計画を登録しました');
    }

    /**
     * 読書計画の編集画面を表示する
     * @param ReadingPlan $readingPlan
     * @return View
     */
    public function edit(ReadingPlan $readingPlan): View
    {
        abort_if($readingPlan->user_id !== Auth::id(), 403);

        return view('reading_plans.edit', compact('readingPlan'));
    }

    /**
     * 読書計画を更新する
     *
     * @param UpdateReadingPlanRequest $request
     * @param ReadingPlan $readingPlan
     * @return RedirectResponse
     */
    public function update(UpdateReadingPlanRequest $request, ReadingPlan $readingPlan): RedirectResponse
    {
        abort_if($readingPlan->user_id !== Auth::id(), 403);

        $this->readingPlanService->updateReadingPlan($readingPlan, $request->validated());

        return redirect()->route('reading-plans.index')->with('success', '読書計画を更新しました');
    }

    /**
     * 読書計画を削除する
     *
     * @param ReadingPlan $readingPlan
     * @return RedirectResponse
     */
    public function destroy(ReadingPlan $readingPlan): RedirectResponse
    {
        abort_if($readingPlan->user_id !== Auth::id(), 403);

        $this->readingPlanService->deleteReadingPlan($readingPlan);

        return redirect()->route('reading-plans.index')->with('success', '読書計画を削除しました');
    }

    /**
     * 読書計画を読了ステータスに変更する
     *
     * @param ReadingPlan
     * @return RedirectResponse
     */
    public function complete(ReadingPlan $readingPlan): RedirectResponse
    {
        abort_if($readingPlan->user_id !== Auth::id(), 403);

        $this->readingPlanService->completeReadingPlan($readingPlan);

        return back()->with('success', '書籍を読了しました');
    }
}