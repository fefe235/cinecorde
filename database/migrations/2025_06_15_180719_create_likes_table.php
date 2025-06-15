<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('critique_id');
            $table->timestamps();
    
            $table->unique(['user_id', 'critique_id']);
    
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('critique_id')->references('id_critique')->on('critiques')->onDelete('cascade');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
