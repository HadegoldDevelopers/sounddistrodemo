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
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index('projects_user_id_foreign');
            $table->string('title');
            $table->enum('type', ['Single', 'EP', 'Album']);
            $table->string('cover_path');
            $table->date('release_date');
            $table->string('genre')->nullable();
            $table->string('subgenre')->nullable();
            $table->string('language')->nullable();
            $table->boolean('explicit')->default(false);
            $table->string('label')->nullable();
            $table->string('songwriter')->nullable();
            $table->string('upc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
