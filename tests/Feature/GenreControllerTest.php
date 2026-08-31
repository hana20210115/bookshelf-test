<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 一覧画面、作成画面、編集画面にアクセスした場合ステータス200が返るか検証
     */
    public function test_画面表示が正常に行われること(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $this->actingAs($user)->get(route('genres.index'))->assertStatus(200);
        $this->actingAs($user)->get(route('genres.create'))->assertStatus(200);
        $this->actingAs($user)->get(route('genres.edit', $genre))->assertStatus(200);
    }

    /**
     * 新規作成、更新、削除の各リクエストを送信した場合、DB.genres_tableのデータが正常に更新され、一覧画面へリダイレクトされるか検証
     */
    public function test_cru_d処理が市場に行われること(): void
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $genre = Genre::factory()->create(['name' => '古いジャンル']);

        // 新規作成
        $this->actingAs($user)
            ->post(route('genres.store'), ['name' => '新しいジャンル'])
            ->assertRedirect(route('genres.create'));
        $this->assertDatabaseHas('genres', ['name' => '新しいジャンル']);

        // 更新
        $this->actingAs($user)
            ->put(route('genres.update', $genre), ['name' => '更新済みのジャンル'])
            ->assertRedirect(route('genres.index'));
        $this->assertDatabaseHas('genres', ['id' => $genre->id, 'name' => '更新済みのジャンル']);

        // 削除
        $this->actingAs($user)
            ->delete(route('genres.destroy', $genre))
            ->assertRedirect(route('genres.index'));
        $this->assertDatabaseMissing('genres', ['id' => $genre->id]);
    }
}
