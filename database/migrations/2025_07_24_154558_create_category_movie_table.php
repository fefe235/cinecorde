<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('category_movie', function (Blueprint $table) {
            $table->unsignedBigInteger('id_movie');
            $table->unsignedBigInteger('id_cat');

            $table->foreign('id_movie')->references('id_movie')->on('movies')->onDelete('cascade');
            $table->foreign('id_cat')->references('id_cat')->on('categories')->onDelete('cascade');

            $table->primary(['id_movie', 'id_cat']); // clé primaire composite
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_movie');
    }
};
