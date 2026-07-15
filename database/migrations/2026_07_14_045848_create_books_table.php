<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->comment('登録ユーザーID');
            $table->string('title')->unique()->comment('書籍タイトル');
            $table->string('author')->comment('著者名');
            $table->text('description',1000)->comment('書籍説明');
            $table->string('image_url')->nullable()->comment('画像URL');
            $table->date('published_at')->comment('出版日');
            $table->string('isbn',13)->nullable()->unique()->comment('ISBN番号');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
