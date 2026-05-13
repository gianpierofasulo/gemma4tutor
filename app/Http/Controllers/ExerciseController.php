<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Exercise;

class ExerciseController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validazione
        $request->validate([
            'file' => 'required|image|max:5000',
            'type' => 'required'
        ]);

        // 2. Salvataggio locale del file
        $path = $request->file('file')->store('exercises', 'public');

        // 3. Preparazione immagine per Gemma (Base64)
        $imageData = base64_encode(Storage::disk('public')->get($path));

        // --- STEP 1: ESTRAZIONE TESTO CON LLAVA ---
        $ocrResponse = Http::timeout(900)->post('http://ollama:11434/api/generate', [
            'model' => 'llava:7b',
            'prompt' => 'Agisci come un sistema OCR. TRASCRIVI esattamente tutto il testo che vedi nell\'immagine senza aggiungere commenti. Attento che il testo può essere scritto in diverse lingue ',
            'images' => [$imageData],
            'stream' => false,
        ]);

        // Controllo se Llava ha risposto correttamente
        if ($ocrResponse->failed()) {
            return back()->with('feedback', 'Errore durante la lettura dell\'immagine: ' . $ocrResponse->body());
        }

        $rawText = $ocrResponse->json()['response'] ?? '';

        if (empty(trim($rawText))) {
            return back()->with('feedback', 'L\'IA non è riuscita a leggere alcun testo nell\'immagine. Riprova con una foto più nitida.');
        }

        // --- STEP 2: ANALISI LINGUISTICA CON GEMMA 2 ---
        // Usiamo un prompt molto direttivo per l'italiano
        $promptGemma = "ISTRUZIONI: Rispondi ESCLUSIVAMENTE in lingua italiana per la SPIEGAZIONE. Sei un tutor esperto.
                        Analizza il seguente testo estratto da un compito: '$rawText'.
                        Fornisci un feedback dettagliato evidenziando errori grammaticali, di sintassi, ortografia e suggerendo correzioni.
                        Segui questo schema per la risposta:
                        1. TESTO RILEVATO: (traduci qui il testo che hai ricevuto e riscrivilo in lingua italiana)
                        2. CORREZIONE: (scrivi la versione corretta nella lingua originale del testo che hai ricevuto)
                        3. SPIEGAZIONE: (spiega gli errori in italiano in modo semplice)";

        $finalResponse = Http::timeout(820)->post('http://ollama:11434/api/generate', [
            'model' => 'gemma2:2b',
            'prompt' => $promptGemma,
            'stream' => false,
            'options' => [
                'temperature' => 0.3, // Più bassa è, più l'IA è precisa e meno "inventa"
            ]
        ]);

        // --- GESTIONE FINALE ---
        if ($finalResponse->failed()) {
            return back()->with('feedback', 'Gemma non risponde: ' . $finalResponse->body());
        }

        $aiFeedback = $finalResponse->json()['response'] ?? 'Errore nella generazione del feedback.';

        // Salvataggio (Assicurati che Exercise abbia i campi fillable nel Model)
        $exercise = Exercise::create([
            'type' => $request->type,
            'file_path' => $path,
            'prompt_sent' => $promptGemma,
            'ai_response' => $aiFeedback,
            'language' => 'it'
        ]);

        return back()->with('success', 'Analisi completata con successo!')
                    ->with('feedback', $aiFeedback);
                        }
}
