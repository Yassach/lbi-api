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
        Schema::create('movie_type', function (Blueprint $table) {
            $table->unsignedBigInteger('movie_id');
            $table->unsignedBigInteger('type_id');
            $table->timestamps();

            $table->primary(['movie_id', 'type_id']);
            $table->foreign('movie_id')->references('id')->on('movies')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('type_id')->references('id')->on('types')
                ->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_type');
    }
};
