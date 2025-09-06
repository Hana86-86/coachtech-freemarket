<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $fkName = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->select('CONSTRAINT_NAME')
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', 'favorites')
            ->where('COLUMN_NAME', 'user_id')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->value('CONSTRAINT_NAME');

        if ($fkName) {
            DB::statement("ALTER TABLE `favorites` DROP FOREIGN KEY `{$fkName}`");
        }

        Schema::table('favorites', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });

        if (!Schema::hasColumn('favorites', 'visitor_token')) {
            Schema::table('favorites', function (Blueprint $table) {
                $table->string('visitor_token', 36)->nullable()->index();
            });
        }

        $hasUnique = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', 'favorites')
            ->where('CONSTRAINT_TYPE', 'UNIQUE')
            ->where('CONSTRAINT_NAME', 'favorites_product_id_visitor_token_unique')
            ->exists();

        if (!$hasUnique) {
            Schema::table('favorites', function (Blueprint $table) {
                $table->unique(['product_id', 'visitor_token'], 'favorites_product_id_visitor_token_unique');
            });
        }

        $fkExistsAgain = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', 'favorites')
            ->where('COLUMN_NAME', 'user_id')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->exists();

        if (!$fkExistsAgain) {
            Schema::table('favorites', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('favorites', 'visitor_token')) {
            Schema::table('favorites', function (Blueprint $table) {
                $table->dropUnique('favorites_product_id_visitor_token_unique');
            });
            Schema::table('favorites', function (Blueprint $table) {
                $table->dropColumn('visitor_token');
            });
        }

        Schema::table('favorites', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });

        $fkExists = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', 'favorites')
            ->where('COLUMN_NAME', 'user_id')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->exists();

        if (!$fkExists) {
            Schema::table('favorites', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }
};