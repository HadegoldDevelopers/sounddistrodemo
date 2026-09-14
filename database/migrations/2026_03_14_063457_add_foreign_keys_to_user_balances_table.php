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
        Schema::table('user_balances', function (Blueprint $table) {
            $table->foreign(['music_id'], 'fk_user_balances_music')->references(['id'])->on('music')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['user_id'], 'fk_user_balances_user')->references(['id'])->on('users')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_balances', function (Blueprint $table) {
            $table->dropForeign('fk_user_balances_music');
            $table->dropForeign('fk_user_balances_user');
        });
    }
};
