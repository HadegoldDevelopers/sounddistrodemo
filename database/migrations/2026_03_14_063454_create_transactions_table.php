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
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index('fk_deposit_user');
            $table->unsignedBigInteger('plan_id')->index('fk_deposit_plan');
            $table->string('gateway', 50);
            $table->decimal('amount', 15);
            $table->string('original_currency', 10);
            $table->decimal('original_amount', 15);
            $table->string('currency', 10);
            $table->string('reference', 100)->unique('reference');
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
