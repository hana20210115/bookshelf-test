<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Enums\ReadingPlanStatus;
use Carbon\Carbon;

class ReadingPlanEndpointTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 期日に過去日を入力してPOSTした場合バリデーションエラーになるか検証
     * @return void
     */
    public function test_期日に過去日を入力してPOSTした場合バリデーションエラーになること(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $data = [
            'book_id' => $book->id,
            'target_date' => Carbon::yesterday()->format('Y-m-d'),
        ];

        $response = $this->actingAs($user)->post('/reading-plans', $data);


        $response->assertStatus(302);
        $response->assertSessionHasErrors(['target_date']);
    }

    /**
     *  他人が作成した読書計画に対して操作した場合403が返るか検証
     * @return void
     */
    public function 他人が作成した読書計画に対して操作した場合403Forbiddenが返ること(): void
    {
        $owner = User::factory()->create(); 

        $otherUser = User::factory()->create(); 
        $book = Book::factory()->create();

        $plan = ReadingPlan::create([
            'user_id' => $owner->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::IN_PROGRESS,
            'target_date' => Carbon::tomorrow()->format('Y-m-d'),
        ]);



        // 編集画面(GET)
        $this->actingAs($otherUser)
            ->get("/reading-plans/{$plan->id}/edit")
            ->assertStatus(403);

        // 更新(PUT)
        $this->actingAs($otherUser)
            ->put("/reading-plans/{$plan->id}", ['target_date' => Carbon::tomorrow()->format('Y-m-d')])
            ->assertStatus(403);

        // 削除(DELETE)
        $this->actingAs($otherUser)
            ->delete("/reading-plans/{$plan->id}")
            ->assertStatus(403);
    }
}