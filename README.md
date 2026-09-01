# MEXAM

Piattaforma per la creazione, lo svolgimento e la correzione di esami tra docenti e studenti.

Un docente costruisce un archivio di domande (**quiz**), le raggruppa in **librerie** per materia e
difficolta', e ne compone degli **esami** con una finestra temporale. Gli studenti svolgono l'esame
online; il docente ne vede i risultati, li corregge e puo' stamparli in PDF.

- **Stack**: PHP 8.4 · Laravel 13 · MySQL 8 · Inertia.js + React 19 + Tailwind · Vite
- **Ambiente**: Docker Compose, con devcontainer pronto per VS Code

---

## Avvio rapido

Serve solo Docker. Non e' necessario avere PHP, Composer o Node installati.

```bash
git clone <url-del-repo> && cd laravel-app
cp .env.example .env
docker compose up -d --build
```

Vengono avviati quattro servizi: l'app (php-fpm), nginx, MySQL e **phpMyAdmin** per ispezionare il
database.

Il primo avvio richiede qualche minuto: il container installa le dipendenze, genera l'`APP_KEY`,
esegue le migrazioni e popola il database di demo. Quando nei log compare `[mexam] pronto`, l'app e'
su **<http://localhost:8088>**.

```bash
docker compose logs -f app     # per seguire l'avanzamento
```

### Credenziali di demo

| Ruolo    | Email                  | Password   |
|----------|------------------------|------------|
| Docente  | `docente@mexam.test`   | `password` |
| Studente | `studente@mexam.test`  | `password` |

Ci sono altri due studenti (`giulia@mexam.test`, `luca@mexam.test`) con la stessa password.

Il seed crea tre esami, uno per ciascuno stato del ciclo di vita — **in corso** (Matematica: uno
studente puo' aprirlo e consegnarlo subito), **non ancora iniziato** (Informatica) e **concluso**
(Storia, gia' consegnato da due studenti, cosi' la pagina dei risultati ha qualcosa da mostrare).

---

## Sviluppo

### Con il devcontainer (consigliato)

Apri la cartella in VS Code e scegli **Reopen in Container**: l'editor si collega al container `app`
gia' in esecuzione, con PHP, Composer, Node (via una feature del devcontainer, non nel Dockerfile —
non appesantisce l'immagine di produzione) e il codice montato in `/var/www`. Il terminale integrato
e' dentro il container, quindi `php artisan ...`, `composer ...`, `vendor/bin/phpunit`, `npm run dev`
e `npx tsc --noEmit` funzionano tutti direttamente, senza `make` ne' un secondo terminale.

**`make` e Docker no**: il container `app` non ha il CLI Docker (ne' l'accesso al socket dell'host),
quindi qualunque comando che parte da `docker compose` — tutto il `Makefile` — da quel terminale
fallisce con `docker: No such file or directory`. Per quello (avviare/fermare i container, `make
fresh`, ecc.) serve un terminale sull'host, fuori da VS Code Dev Containers: vedi la sezione seguente.
Per lavorare su PHP e frontend, pero', non serve mai uscire dal terminale del devcontainer.

### Con Docker da terminale

Da un terminale **sull'host** (non quello integrato di un Dev Container gia' connesso ad `app`: li'
dentro non c'e' il CLI Docker). Il `Makefile` copre i comandi ricorrenti:

```bash
make up            # avvia l'app
make shell         # shell dentro il container
make test          # esegue la suite
make fresh         # ricrea il database e ripopola i dati di demo
make pint          # formatta il codice
make dev           # Vite in hot reload su :5273
make dbshell       # client MySQL da terminale
make down          # ferma tutto
make help          # elenco completo
```

Senza `make`, ogni comando e' un `docker compose exec app <comando>`:

```bash
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app vendor/bin/phpunit
```

### Frontend

Gli asset compilati sono gia' dentro l'immagine, quindi l'app funziona appena avviata. Per lavorare
sul CSS/JS con hot reload serve Vite in watch mode.

**Nel devcontainer** (Node e' li' dentro, vedi sopra): direttamente nel terminale integrato,
`git pull` compreso — se il commit ha aggiornato le dipendenze JS (come lo Step 16, che ha introdotto
Ziggy e i pacchetti Radix mancanti), un `npm install` prima basta.

```bash
npm install       # solo se package.json e' cambiato
npm run dev       # Vite in hot reload, porta gia' forwardata dal devcontainer
npx tsc --noEmit  # controllo dei tipi, senza tenere Vite acceso
```

**Da un terminale sull'host** (non devcontainer, vedi sotto): stesso risultato via il servizio `node`
del compose, con `make`:

```bash
make dev    # oppure: docker compose --profile dev up node — esegue npm install a ogni avvio
make types  # tsc --noEmit dentro il servizio node
```

Nessuno dei due e' ancora agganciato alla CI (`vendor/bin/phpunit` e' l'unico step in
`.github/workflows/tests.yml` al momento): lanciare il type-check a mano prima di aprire una PR che
tocca `resources/js/`.

### Porte

Volutamente non standard, perche' 80, 8080, 9000, 6033 e 8081 sono spesso gia' occupate da altri
progetti. Si cambiano in `.env`, senza toccare il `docker-compose.yml`.

| Servizio    | Host                      | Variabile       |
|-------------|---------------------------|-----------------|
| App         | <http://localhost:8088>   | `APP_PORT`      |
| MySQL       | `127.0.0.1:33061`         | `DB_PORT_HOST`  |
| Vite HMR    | `127.0.0.1:5273`          | `VITE_PORT`     |
| phpMyAdmin  | <http://localhost:8181>   | `PMA_PORT`      |

---

## Test

```bash
make test
```

La suite gira su SQLite in memoria (`phpunit.xml`), quindi non tocca il database di sviluppo.

---

## Struttura

```
app/Http/Controllers/    CustomAuth, Quiz, Library, LibraryQuiz, Exam, ExamQuiz, Profile
app/Models/              User, Quiz, Library, Exam, UserAnswer
app/Http/Middleware/     IsTeacher, IsStudent
database/seeders/        dataset di demo coerente (utenti, quiz, librerie, esami)
resources/js/pages/      pagine React (Inertia), una per rotta; layout condiviso in layouts/app-layout.tsx
resources/views/         solo il root Inertia (app.blade.php) e i due template di stampa PDF
docker/                  entrypoint del container app
```

### Modello dei dati

```
User ──< exam_user >── Exam ──< exam_quiz >── Quiz ──< library_quiz >── Library
                         │                     │
                         └──── UserAnswer ─────┘
```

Un `Quiz` e' **a risposta chiusa** (`answer`, vero/falso) **oppure a risposta aperta**
(`answer_text`), mai entrambe: e' l'invariante su cui si basano le view di svolgimento e la
correzione.

---

## Note operative

- **Riavvii successivi**: `docker compose up -d` non rifa' il seed. Per ripartire dai dati di demo,
  `make fresh`.
- **Avvio senza automatismi**: `MEXAM_AUTO_SETUP=false` in `.env` fa partire php-fpm senza eseguire
  `composer install`, migrazioni e seed.
- **Su Linux**: allinea `UID`/`GID` in `.env` al tuo utente (`id -u`, `id -g`) per non ritrovarti
  file root-owned nel repo. Su macOS lasciali invariati.

---

## Deploy pubblico

`.env.example` e' pensato per lo sviluppo locale (`APP_DEBUG=true` e' corretto li', l'ambiente non e'
raggiungibile da Internet). Per un deploy su dominio pubblico parti invece da `.env.production.example`,
che imposta `APP_DEBUG=false`, cookie di sessione solo su HTTPS e gli altri default di produzione, e
compila i placeholder (database, mail, eventuali proxy davanti all'app).

Non usare il `docker-compose.yml` di questo repo cosi' com'e' per un deploy pubblico: espone la porta
MySQL sull'host e pubblica phpMyAdmin, entrambi pensati solo per lo sviluppo locale.
