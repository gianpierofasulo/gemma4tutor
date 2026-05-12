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
    Schema::create('exercises', function (Blueprint $column) {
        $column->id();
        $column->string('type'); // 'image' o 'audio'
        $column->string('file_path'); // Dove salviamo il file fisico
        $column->text('prompt_sent'); // Cosa abbiamo chiesto a Gemma
        $column->text('ai_response')->nullable(); // La correzione di Gemma
        $column->string('language')->default('en'); // Lingua studiata
        $column->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
