<?php

namespace Tests\Feature;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 一覧画面、作成画面、編集画面にアクセスした場合、ステータス200が返るか検証
     */
    public function test_画面表示が正常に行われること(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $plan = ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => now()->addDays(3)->format('Y-m-d'),
            'status' => ReadingPlanStatus::InProgress,
        ]);

        $this->actingAs($user)->get(route('reading-plans.index'))->assertStatus(200);
        $this->actingAs($user)->get(route('reading-plans.create'))->assertStatus(200);
        $this->actingAs($user)->get(route('reading-plans.edit', $plan))->assertStatus(200);
    }

    /**
     * 新規作成、更新、読了、削除の各リクエストを送信した場合、DBやステータスが正常に処理され、適切な画面へリダイレクトされるか検証
     */
    public function test_cru_d処理および読了アクションが正常に行われること(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $plan = ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => now()->addDays(3)->format('Y-m-d'),
            'status' => ReadingPlanStatus::InProgress,
        ]);

        // 作成

        $targetDateStore = now()->addDays(5)->format('Y-m-d');

        $this->actingAs($user)
            ->post(route('reading-plans.store'), [
                'book_id' => $book->id,
                'target_date' => $targetDateStore,
            ])
            ->assertRedirect(route('reading-plans.create'));

        $this->assertDatabaseHas('reading_plans', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => $targetDateStore,
            // 新規作成時はデフォルトで InProgress になると想定
            'status' => ReadingPlanStatus::InProgress,
        ]);

        // 更新

        $targetDateUpdate = now()->addDays(10)->format('Y-m-d');

        $this->actingAs($user)
            ->put(route('reading-plans.update', $plan), [
                'target_date' => $targetDateUpdate,
            ])
            ->assertRedirect(route('reading-plans.index'));

        $this->assertDatabaseHas('reading_plans', [
            'id' => $plan->id,
            'target_date' => $targetDateUpdate,
        ]);

        // 読了

        $this->actingAs($user)
            ->post(route('reading-plans.complete', $plan))
            ->assertRedirect();

        $this->assertDatabaseHas('reading_plans', [
            'id' => $plan->id,
            'status' => ReadingPlanStatus::COMPLETED,
        ]);

        // 削除

        $this->actingAs($user)
            ->delete(route('reading-plans.destroy', $plan))
            ->assertRedirect(route('reading-plans.index'));

        $this->assertDatabaseMissing('reading_plans', [
            'id' => $plan->id,
        ]);
    }
}
