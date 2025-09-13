<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('category_product') && Schema::hasColumn('products', 'category_id')) {
            DB::table('products')
                ->select('id', 'category_id')
                ->whereNotNull('category_id')
                ->orderBy('id')
                ->chunkById(500, function ($rows) {
                    foreach ($rows as $row) {
                        DB::table('category_product')->updateOrInsert(
                            ['product_id' => $row->id, 'category_id' => $row->category_id],
                            ['created_at' => now(), 'updated_at' => now()]
                        );
                    }
                });

            Schema::table('products', function (Blueprint $table) {
                $table->dropConstrainedForeignId('category_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     * 構造だけ戻す（データは戻さない）
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'category_id')) {
                $table->foreignId('category_id')
                    ->nullable()
                    ->constrained()
                    ->nullOnDelete();
            }
        });
    }
};