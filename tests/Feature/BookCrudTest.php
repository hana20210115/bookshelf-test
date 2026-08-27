<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use Illuminate\Support\Facades\DB;


class BookCrudTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログインユーザーが正しいデータを送信した場合、DBにデータが保存されて、書籍登録画面にリダイレクトするか検証
     *@return void
     */
    public function test_正しいデータを送信した場合DBに保存され書籍登録画面に戻る():void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create(['name' => 'テストジャンル']);

        $bookData = [
            'title' => 'テストタイトル',
            'author' => 'テスト著者',
            'genres' => [$genre->id],
            'description'=> 'テスト説明文',
            'isbn' => '1234567890123',
            'published_date' => '2023-01-01',
        ];

        //ユーザーとしてログインして、POSTリクエスト送信
        $response = $this->actingAs($user)->post('/books',$bookData);

        //ステータスが302で、書籍登録画面に遷移しているか
        $response->assertStatus(302)->assertRedirect('/books/create');

        //データベースに送信したデータが保存されているか確認
        $this->assertDatabaseHas('books',[
            'title' => 'テストタイトル',
            'author' => 'テスト著者',
            'description'=> 'テスト説明文',
            'isbn' => '1234567890123',
            'published_date' => '2023-01-01',
            ]);
        
        $this->assertDatabaseHas('book_genre',[
            'genre_id' => $genre->id,

        ]);

    }

    /**
     * 登録時に必須項目を空にして送った場合、バリデーションエラーになるか検証　302 Found
     * @return
     */
    public function test_登録時必須項目を空にして送信した場合バリデーションエラーになるか():void
    {
        $user = User::factory()->create();

        //空の配列を渡す
        $response = $this->actingAs($user)->post('/books',[]);

        //その場にリダイレクトされ、必須項目にバリデーションエラーが発生しているか確認
        $response->assertStatus(302)->assertInvalid([
            'title' => 'タイトルを入力してください',
            'author' => '著者名を入力してください',
            'genres' => 'ジャンルを選択してください',
        ]);
    }

    /**
     * ログインユーザーが、自分で作成した書籍のISBNを更新処理をした場合、重複エラーに引っかからないか検証
    *@return void
    */
    public function test_自分が作成した書籍のISBNを変更せずに更新した場合重複エラーにならず成功する():void{
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
            'isbn' => '1234567890123',
        ]);

        $updateData = [
            'title' => '変更後のテストタイトル',
            'author' => $book->author,
            'isbn' => $book->isbn,
            'description' => $book->description,
            'published_date' => $book->published_date,
            'genres' => [$genre->id],
        ];

        //更新リクエスト
        $response = $this->actingAs($user)->put("/books/{$book->id}",$updateData);

        //エラーにならずにリダイレクトされる
        $response->assertStatus(302)->assertRedirect("/books/{$book->id}");

        //データベースが更新されているか
        $this->assertDatabaseHas('books',[
            'user_id' => $user->id,
            'title' => '変更後のテストタイトル',

        ]);
    }

    /**
     * 更新時に必須項目を空にして送った場合、バリデーションエラーになるか検証　302 Found
     * @return void
     */
    public function test_更新時必須項目を空にして送信した場合バリデーションエラーになるか():void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        

        $response = $this->actingAs($user)->put("/books/{$book->id}",[]);

        $response->assertStatus(302)->assertInvalid([
            'title' => 'タイトルを入力してください',
            'author' => '著者名を入力してください',
            'genres' => 'ジャンルを選択してください',
        ]);
    }

    /**
     * 正しい編集データを送信した場合、DBに保存され、書籍詳細画面にリダイレクトするか検証　302 Found
     * @return void
     */
    public function test_正しい編集データを送信した場合DBに保存され書籍詳細画面にリダイレクトする(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $updateData = [
            'title' => '新しいタイトル',
            'author' => '新しい著者',
            'genres' => [$genre->id],
            'description' => '新しい説明文',
            'isbn' => '9999999999999',
            'published_date' => '2025-12-31',
        ];

        $response = $this->actingAs($user)->put("/books/{$book->id}",$updateData);

        $response -> assertStatus(302)->assertRedirect("/books/{$book->id}");
        
        $this->assertDatabaseHas('books',[
            'id' => $book->id,
            'title' => '新しいタイトル',
            'author' => '新しい著者',
            'description' => '新しい説明文',
            'isbn' => '9999999999999',
            'published_date' => '2025-12-31',
        ]);

        $this->assertDatabaseHas('book_genre',[
            'genre_id' => $genre->id,

        ]);
    }

    /**
     * すでに使っているISBNを送信した場合、元の画面へリダイレクトしバリデーションエラーが表示されるか検証
     * @return void
     * 
     */
    public function test_すでに使われているISBNを送信した場合バリデーションエラーになる():void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        //事前にテストISBNをDBに登録しておく
        Book::factory()->create(['isbn' => '8888888888888']);

        $bookData = [
            'title' => '新しいタイトル2',
            'author' => '新しい著者2',
            'genres' => [$genre->id],
            'isbn' => '8888888888888',
        ];

        $response = $this->actingAs($user)->post('/books',$bookData);

        $response->assertStatus(302)->assertInvalid(['isbn' => '有効なISBNを入力してください']);

    }

    

    /**
     * 書籍削除処理を行った場合、データおよび紐づくデータが削除され、書籍一覧画面へリダイレクトするか検証
     *@return void
     */
    public function test_書籍を削除した場合紐づくデータも削除され一覧画面へリダイレクトする():void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        //中間テーブルに紐付け
        $book->genres()->attach($genre->id);
        
        //レビュー作成
        $review = Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);

        //お気に入りを作成、中間テーブルのモデルがないからテーブルに直接データを入れる
        DB::table('favorites')->insert([
            'book_id' => $book->id,
            'user_id' => $user->id,          
            ]);
        

        $response = $this->actingAs($user)->delete("/books/{$book->id}/delete");

        $response->assertStatus(302)->assertRedirect('/books');

        $this->assertDatabaseMissing('books',['id' => $book->id]);
        $this->assertDatabaseMissing('book_genre',['book_id' => $book->id]);
        $this->assertDatabaseMissing('reviews',['id' => $review->id]);
        $this->assertDatabaseMissing('favorites',['book_id' => $book->id]);
    }

    /**
     * 書籍一覧でキーワード検索がただいく機能するか検証
     * 
     * @return void
     */
    public function test_書籍一覧でキーワード検索が正しく機能するか(): void
    {
        $user = User::factory()->create();

        Book::factory()->create(['title' => 'Laravel完全マスター', 'author' => '田中太郎']);

        Book::factory()->create(['title' => 'PHP基礎大全', 'author' => '鈴木次郎']);
        Book::factory()->create(['title' => 'はじめてのDocker', 'author' => '田中二郎']);


        $response = $this->actingAs($user)->get('/books?keyword=田中');


        $response->assertStatus(200);

        $response->assertSee('Laravel完全マスター');
        $response->assertSee('はじめてのDocker');
        $response->assertDontSee('PHP基礎大全');
    }

    /**
     * 書籍一覧でソート機能が正しく機能するか検証
     * @return void
     */
    public function test_書籍一覧でソート機能が正しく機能するか(): void
    {
        $user = User::factory()->create();


        $book1 = Book::factory()->create(['title' => 'Aの本', 'created_at' => now()->subDays(3)]);
        $book2 = Book::factory()->create(['title' => 'Bの本', 'created_at' => now()->subDays(2)]);
        $book3 = Book::factory()->create(['title' => 'Cの本', 'created_at' => now()->subDays(1)]);


        $response = $this->actingAs($user)->get('/books?sort=oldest');

        $response->assertStatus(200);


        $response->assertSeeInOrder([
            'Aの本',
            'Bの本',
            'Cの本',
        ]);
    }
}
