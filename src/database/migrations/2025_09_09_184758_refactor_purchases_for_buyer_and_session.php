<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {

            try { $table->dropForeign(['user_id']); } catch (\Throwable $e) {}

            if (!Schema::hasColumn('purchases', 'buyer_id')) {
                $table->unsignedBigInteger('buyer_id')->nullable()->after('id');
            }

            if (!Schema::hasColumn('purchases', 'session_id')) {
                $table->string('session_id', 255)->nullable()->after('payment_intent_id');
                $table->unique('session_id', 'purchases_session_id_unique');
            }
        });

        DB::table('purchases')->whereNull('buyer_id')->update([
            'buyer_id' => DB::raw('user_id')
        ]);

        Schema::table('purchases', function (Blueprint $table) {

            $table->unsignedBigInteger('buyer_id')->nullable(false)->change();
            $table->foreign('buyer_id')->references('id')->on('users')->onDelete('cascade');

            if (Schema::hasColumn('purchases', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {

            try { $table->dropForeign(['buyer_id']); } catch (\Throwable $e) {}

            if (!Schema::hasColumn('purchases', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }
        });

        DB::table('purchases')->whereNull('user_id')->update([
            'user_id' => DB::raw('buyer_id')
        ]);

        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            if (Schema::hasColumn('purchases', 'buyer_id')) {
                $table->dropColumn('buyer_id');
            }

            try { $table->dropUnique('purchases_session_id_unique'); } catch (\Throwable $e) {}
            if (Schema::hasColumn('purchases', 'session_id')) {
                $table->dropColumn('session_id');
            }
        });
    }
};