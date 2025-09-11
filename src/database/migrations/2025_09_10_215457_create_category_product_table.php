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
        if (!\Illuminate\Support\Facades\Schema::hasTable('category_product')) {
        \Illuminate\Support\Facades\Schema::create('category_product', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['product_id', 'category_id']);
        });
    }
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (\Illuminate\Support\Facades\Schema::hasTable('category_product')) {
        \Illuminate\Support\Facades\Schema::drop('category_product');
    }
    }
};
