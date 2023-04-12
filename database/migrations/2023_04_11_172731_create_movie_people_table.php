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
        Schema::create('movie_people', function (Blueprint $table) {
            $table->unsignedBigInteger('movie_id');
            $table->unsignedBigInteger('people_id');
            $table->string('role', 255);
            $table->enum('significance', ['principal', 'secondaire'])->nullable();
            $table->timestamps();

            $table->primary(['movie_id', 'people_id']);
            $table->foreign('movie_id')->references('id')->on('movies')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('people_id')->references('id')->on('people')
                ->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_people');
    }
};
