<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\ExerciseController;

Route::post('/upload-exercise', [ExerciseController::class, 'store']);

Route::get('/', function () {
    return view('welcome');
});


Route::get('/test-ai', function () {
    try {
        $response = Http::timeout(60)->post('http://ollama:11434/api/generate', [
            'model' => 'gemma2:2b', // Assicurati di aver fatto "ollama run gemma2:2b"
            'prompt' => 'Sei un tutor di lingue. Saluta l\'utente in 3 lingue diverse in modo amichevole.',
            'stream' => false,
        ]);

        if ($response->successful()) {
            return $response->json()['response'];
        }

        return "Errore da Ollama: " . $response->body();
        
    } catch (\Exception $e) {
        return "Errore di connessione: " . $e->getMessage();
    }
});
