<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * マイ読書レポート画面にアクセスした場合、ステータス200が返るか検証
     *
     * @return void
     */
    public function test_マイ読書レポート画面の表示が正常に行われること(): void
    {
        $user = User::factory()->create();
        
        $this->actingAs($user)->get(route('reports.index'))->assertStatus(200);
    }
}
