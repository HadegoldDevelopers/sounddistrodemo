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
        if (Schema::hasTable('homepage_contents')) {
            return;
        }

        Schema::create('homepage_contents', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('hero_title')->nullable();
            $table->text('hero_text')->nullable();
            $table->string('hero_image')->nullable();

            $table->string('features_title')->nullable();
            $table->text('features_text')->nullable();
            $table->json('features')->nullable();

            $table->string('labels_title')->nullable();
            $table->text('labels_text')->nullable();
            $table->string('labels_image')->nullable();

            $table->string('pricing_title')->nullable();
            $table->text('pricing_text')->nullable();

            $table->json('artists')->nullable();

            $table->string('cta_title_1')->nullable();
            $table->string('cta_title_2')->nullable();
            $table->text('cta_text')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homepage_contents');
    }
};