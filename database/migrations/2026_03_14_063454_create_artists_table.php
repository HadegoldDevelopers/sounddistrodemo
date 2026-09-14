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
        Schema::create('artists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->text('bio')->nullable();
            $table->string('genre')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('audiomack_id')->nullable();
            $table->string('spotify_id')->nullable();
            $table->string('apple_music_id')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->string('tiktok')->nullable();
            $table->unsignedBigInteger('user_id')->index('artists_user_id_foreign');
            $table->unsignedBigInteger('label_id')->nullable()->index('fk_artist_label');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artists');
    }
};
