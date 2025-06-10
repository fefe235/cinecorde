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
        Schema::create('critiques', function (Blueprint $table) {
            $table->id("id_critique");
            $table->foreignId('id_movie')->constrained('movies', 'id_movie');
            $table->foreignId('id_user')->constrained('users','user_id');
            $table->decimal('note',4,3);
            $table->text("critique");
            $table->integer("nbr_like");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('critiques');
    }
};
