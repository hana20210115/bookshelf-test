<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 登録されているメールアドレスとパスワードでログインした場合、書籍一覧画面へリダイレクトされるか検証　302 Found
     * @return void
     */
    public function test_登録されているメールアドレスとパスワードでログインした場合書籍一覧画面へリダイレクトされるか():void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);
        
        $response = $this->post('/login',[
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(302);

        $response->assertRedirect('/books');

        //認証されているか確認するメソッド
        $this->assertAuthenticatedAs($user);

    }

    /**
     * 登録されていないユーザー情報でログインした場合
     * ログイン画面へリダイレクトするか検証　302 Found
     * @return void
     */
    public function test_登録されていないユーザー情報でログインした場合ログイン画面へリダイレクトするか検証():void
    {
        $response = $this->post('/login',[
            'email' => 'notfound@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/');

        $response->assertInvalid([
            'email' => 'メールアドレスまたはパスワードが間違っています'
        ]);

        $this->assertGuest();
    }
}
