# MEXAM

Piattaforma per la creazione, lo svolgimento e la correzione di esami tra docenti e studenti.

Un docente costruisce un archivio di domande (**quiz**), le raggruppa in **librerie** per materia e
difficolta', e ne compone degli **esami** con una finestra temporale. Gli studenti svolgono l'esame
online; il docente ne vede i risultati, li corregge e puo' stamparli in PDF.

- **Stack**: PHP 8.2 · Laravel 10 · MySQL 8 · Blade + Bootstrap 5 · Vite
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
gia' in esecuzione, con PHP, Composer, le estensioni e il codice montato in `/var/www`. Il terminale
integrato e' dentro il container, quindi `php artisan ...` funziona direttamente.

Node non e' installato nell'immagine PHP: per il frontend si usa il servizio `node` (`make dev`).

### Con Docker da terminale

Il `Makefile` copre i comandi ricorrenti:

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
sul CSS/JS con hot reload serve il servizio Vite:

```bash
make dev           # oppure: docker compose --profile dev up node
```

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
resources/views/         Blade, layout in auth/layouts.blade.php
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
