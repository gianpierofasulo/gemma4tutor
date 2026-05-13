<!DOCTYPE html>
<html lang="it">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Gemma Lang Tutor</title>
    <style>
        #page-preloader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(2px);
        }
    </style>
</head>
<body class="bg-light">
    <div id="page-preloader" aria-live="polite" aria-busy="true">
        <div class="text-center">
            <div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
            <p class="mt-3 mb-0 fw-semibold">Analisi in corso, attendi la risposta...</p>
        </div>
    </div>

    <div class="container py-5">
        <h1 class="mb-4">Gemma 4 Language Tutor</h1>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">Carica il tuo compito (Foto)</h5>
                <form id="exercise-upload-form" action="/upload-exercise" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="type" value="image">
                    <div class="mb-3">
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Analizza con Gemma 4</button>
                </form>
            </div>
        </div>

        @if(session('feedback'))
                <div class="card mt-4 border-success">
                    <div class="card-header bg-success text-white">
                        Risultato dell'analisi di Gemma 4
                    </div>
                    <div class="card-body">
                        <p style="white-space: pre-wrap;">{{ session('feedback') }}</p>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success mt-2">
                    {{ session('success') }}
                </div>
            @endif
        <div id="results"></div>
    </div>

    <script>
        const uploadForm = document.getElementById('exercise-upload-form');
        const preloader = document.getElementById('page-preloader');

        uploadForm.addEventListener('submit', function () {
            preloader.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });

        window.addEventListener('pageshow', function () {
            preloader.style.display = 'none';
            document.body.style.overflow = '';
        });
    </script>
</body>
</html>
