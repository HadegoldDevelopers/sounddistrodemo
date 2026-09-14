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
        Schema::create('music', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id')->nullable()->index('music_project_id_foreign');
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->string('artist');
            $table->string('featured_artists')->nullable();
            $table->string('genre');
            $table->date('release_date');
            $table->text('credits')->nullable();
            $table->string('upc')->nullable();
            $table->string('isrc')->nullable();
            $table->string('status')->default('pending');
            $table->string('cover_path')->nullable();
            $table->string('audio_path')->nullable();
            $table->integer('track_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('music');
    }
};
