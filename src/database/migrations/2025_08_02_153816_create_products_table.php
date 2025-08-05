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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title', 255);
            $table->text('description');
            $table->integer('price');
            $table->string('category', 100)->nullable();
            $table->string('image_path', 255);
            $table->enum('condition', [
                '良好',
                '目立った傷や汚れなし',
                'やや傷や汚れあり',
                '状態が悪い'
            ])->default('良好');
            $table->enum('sale_status', ['公開中', '取引中', '売却済'])->default('公開中');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
