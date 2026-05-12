<!DOCTYPE html>
<html lang="it">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Gemma Lang Tutor</title>
</head>
<body class="bg-light">
    <div class="container py-5">
        <h1 class="mb-4">Gemma 4 Language Tutor</h1>
        
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">Carica il tuo compito (Foto)</h5>
                <form action="/upload-exercise" method="POST" enctype="multipart/form-data">
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
</body>
</html>
