<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 正しい形式のデータが送信された場合、(DB.users_table)に保存され書籍一覧画面へリダイレクトされるか検証　302 Found
     */

    public function test_正しい形式のデータが送信された場合DBに保存され書籍一覧画面へリダイレクトされる():void
    {
        $response = $this->post('/register',[
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(302);

        $response->assertRedirect('/books');

        $this->assertDatabaseHas('users',[
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
        ]);
        
        $this->assertAuthenticated();
    }

    /**
     * すでに登録されているメールアドレスの場合、登録画面へ戻りバリデーションエラーが表示されることを検証　302 Found
     * @return void
     */
    public function test_すでに登録されているメールアドレスの場合登録画面へ戻りバリデーションエラーが表示されるか():void
    {
        User::factory()->create([
            'email' => 'duplicate@example.com',
        ]);



        $response = $this->from('/register')->post('/register',[
            'name' => 'テスト太郎',
            'email' => 'duplicate@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);



        $response->assertStatus(302);
        $response->assertRedirect('/register');
        $response->assertInvalid([
            'email' => '有効なメールアドレスを入力してください',
        ]);
    }

    /**
     * 各項目が空で送信された場合、登録画面へリダイレクトしバリデーションエラーが表示される　302 Found
     * @return void
     */
    public function test_各項目が空で送信された場合登録画面へリダイレクトしバリデーションエラーが表示される():void
    {
        $response = $this->from('/register')->post('/register',[
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/register');
        $response->assertInvalid([
            'name' => '名前を入力してください',
            'email' => 'メールアドレスを入力してください',
            'password' => 'パスワードを入力してください',
        ]);
    }
    
    /**
     * パスワードが7文字以下で送信された場合、DBに保存されず登録画面へリダイレクトしバリデーションエラーが表示される 302 Found
     * @return void
     */
    public function test_パスワードが7文字以下で送信された場合DBに保存されずにバリデーションエラーが表示される():void
    {
        $response = $this->from('/register')->post('/register',[
            'name' => 'テスト花子',
            'email' => 'new@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/register');
        $response->assertInvalid([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);

        $this->assertDatabaseMissing('users',[
            'name' => 'テスト花子',
            'email' => 'new@example.com',
        ]);
    }

    /**
     * パスワードと確認用パスワードが一致しない場合、DBには保存されず登録画面にリダイレクトし、バリデーションエラーが表示されるか検証　302 Found
     * @return void
     */
    public function test_パスワードと確認用パスワードが一致しない場合登録画面へリダイレクトしバリデーションエラーが表示されるか():void
    {
        $response=$this->from('/register')->post('/register',[
            'name' => 'テスト野郎',
            'email' => 'mismatch@example.com',
            'password' => 'password',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/register');
        $response->assertInvalid([
            'password' => 'パスワードと一致しません',
        ]);

        $this->assertDatabaseMissing('users',[
            'name' => 'テスト野郎',
            'email' => 'mismatch@example.com',
        ]);
    }
}
