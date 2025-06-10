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
        Schema::create('movies', function (Blueprint $table) {
            $table->id('id_movie');
            $table->unsignedBigInteger('tmdb_id')->unique();
            $table->string("slug")->unique();
            $table->string('movie_title');
            $table->text("synopsis");
            $table->string("year");
            $table->text("casting");
            $table->string("image");
            $table->string("trailler")->nullable();
            $table->decimal("avg_note",4,3);
            $table->foreignId('id_cat')->constrained('categories','id_cat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
