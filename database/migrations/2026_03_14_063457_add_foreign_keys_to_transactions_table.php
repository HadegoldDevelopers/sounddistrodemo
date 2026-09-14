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
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign(['plan_id'], 'fk_deposit_plan')->references(['id'])->on('subscription_plans')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['user_id'], 'fk_deposit_user')->references(['id'])->on('users')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign('fk_deposit_plan');
            $table->dropForeign('fk_deposit_user');
        });
    }
};
