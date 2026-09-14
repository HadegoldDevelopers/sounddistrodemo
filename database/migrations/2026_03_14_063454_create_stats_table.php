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
        Schema::create('stats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index('stats_user_id_foreign');
            $table->unsignedBigInteger('artist_id')->nullable();
            $table->year('year');
            $table->unsignedTinyInteger('month');
            $table->unsignedBigInteger('music_id')->index('stats_music_id_foreign');
            $table->string('store');
            $table->string('country', 50);
            $table->string('quality');
            $table->unsignedBigInteger('streams')->default(0);
            $table->decimal('earnings', 20, 8)->default(0);
            $table->string('import_hash');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stats');
    }
};
