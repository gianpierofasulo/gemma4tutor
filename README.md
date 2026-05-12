# 📘 GemmaKnow: AI Language Tutor (WSL2 & Docker)

GemmaKnow è un tutor linguistico intelligente basato su Laravel 11 che utilizza modelli AI locali (Gemma e Llava) per analizzare compiti scritti, trascriverli tramite OCR e fornire correzioni grammaticali dettagliate in italiano.

Questo progetto è ottimizzato per girare interamente in locale su Windows tramite WSL2, garantendo privacy assoluta e zero costi di API cloud.

---

## 🛠️ Requisiti Preliminari

Prima di iniziare, assicurati di avere:

- **Docker Desktop** installato su Windows. [Scaricalo qui](https://www.docker.com/products/docker-desktop)
- Nelle impostazioni di Docker Desktop, vai su **Resources > WSL Integration** e abilita l'integrazione per la tua distribuzione (es. Ubuntu)
- **RAM Fisica**: Almeno 16GB (consigliati per far girare i modelli LLM)

---

## 🏗️ Passo 0: Installazione e Configurazione WSL2

Se non hai ancora installato il Sottosistema Windows per Linux (WSL), apri PowerShell come amministratore e digita:

```powershell
wsl --install
```

Riavvia il computer se richiesto. Una volta installata la distribuzione (es. Ubuntu), apri il terminale della tua Ubuntu.

### Ottimizzazione RAM per l'IA

Di default WSL limita la RAM. Per far girare i modelli senza crash, crea/modifica il file `.wslconfig` nel tuo profilo utente Windows (`C:\Users\TuoUtente\.wslconfig`):

```ini
[wsl2]
memory=12GB  # Alloca almeno 12GB di RAM per WSL
processors=4 # Numero di core dedicati
```

Poi riavvia WSL da PowerShell:

```powershell
wsl --shutdown
```

---

## 📥 Passo 1: Clonare il Progetto

Apri il terminale di Ubuntu (WSL) e spostati nella tua home.
**Importante**: Non installare il progetto nei path di Windows (es. `/mnt/c/`), ma rimani nel filesystem di Linux per avere prestazioni ottimali.

```bash
cd ~
git clone https://github.com/gianpierofasulo/gemma4tutor
cd gemma4tutor
```

---

## 🐳 Passo 2: Avvio dei Container Docker

Costruisci e avvia l'infrastruttura (Laravel, MySQL, Ollama):

```bash
docker-compose up -d --build
```

Controlla che tutti i servizi siano attivi:

```bash
docker-compose ps
```

---

## 🧠 Passo 3: Configurazione Modelli AI (Ollama)

Dobbiamo scaricare i "cervelli" dell'applicazione all'interno del container di Ollama:

```bash
# Modello per la visione (OCR)
docker exec -it gemma4tutor-ollama-1 ollama pull llava:7b

# Modello per l'analisi linguistica (Gemma)
docker exec -it gemma4tutor-ollama-1 ollama pull gemma2:2b
```

---

## 🚀 Passo 4: Inizializzazione Laravel

Esegui i comandi di configurazione standard all'interno del container dell'applicazione:

```bash
# 1. Installazione dipendenze PHP
docker-compose exec app composer install

# 2. Configurazione file .env
docker-compose exec app cp .env.example .env

# 3. Generazione chiave di sicurezza
docker-compose exec app php artisan key:generate

# 4. Migrazione del Database (Crezione tabelle)
docker-compose exec app php artisan migrate
```

### Configurazione delle Variabili d'Ambiente

Apri il file `.env` (puoi usare VS Code con estensione WSL) e assicurati che i puntamenti siano corretti:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=polyglot_db
DB_USERNAME=root
DB_PASSWORD=password

OLLAMA_URL=http://ollama:11434
```

---

## 📂 Passo 5: Permessi e File System (Fix WSL2)

Per evitare errori di scrittura dei log o della cache, esegui questi comandi per allineare i permessi delle cartelle di Laravel:

```bash
# Crea le sottocartelle necessarie
docker-compose exec app mkdir -p storage/framework/{sessions,views,cache/data}

# Assegna i permessi all'utente del server web (www-data)
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache

# Collega lo storage pubblico per le immagini
docker-compose exec app php artisan storage:link
```

---

## 🏃 Passo 6: Avvio dell'Applicazione

Ora puoi far partire il server di sviluppo:

```bash
docker-compose exec app php artisan serve --host=0.0.0.0 --port=80
```

L'applicazione sarà raggiungibile su Windows all'indirizzo:

👉 **http://localhost:8000**

---

## 💡 Note Tecniche

- **Inference Time**: L'analisi di una foto può richiedere da 30 a 60 secondi a seconda della potenza del tuo processore.
- **Double-Pass**: Il sistema prima usa Llava per trascrivere il testo e poi passa il risultato a Gemma per la correzione grammaticale.
