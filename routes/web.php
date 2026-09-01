<?php

use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamQuizController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\LibraryQuizController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Le rotte erano una lista piatta in cui il middleware andava ripetuto su ogni
| riga: due rotte erano rimaste senza alcuna protezione e una decina senza il
| controllo di ruolo. Raggruppandole, il modello dei permessi si legge in
| verticale ed e' molto piu' difficile che una nuova rotta nasca sguarnita.
|
| Struttura:
|   - pubbliche      landing page
|   - guest          login e registrazione
|   - auth           qualunque utente autenticato
|   - auth+isTeacher solo docenti
|
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : view('welcome');
});

/*
| Ospiti
*/
Route::middleware('guest')->group(function () {
    Route::get('login', [CustomAuthController::class, 'index'])->name('login');
    Route::get('register', [CustomAuthController::class, 'registration'])->name('register');

    // Non c'era alcun rate limiting: le credenziali erano attaccabili a forza
    // bruta senza limiti.
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('custom-login', [CustomAuthController::class, 'customLogin'])->name('login.custom');
        Route::post('custom-registration', [CustomAuthController::class, 'customRegistration'])->name('register.custom');
    });
});

/*
| Utenti autenticati (studenti e docenti)
*/
Route::middleware('auth')->group(function () {
    Route::post('signout', [CustomAuthController::class, 'signOut'])->name('signout');

    Route::get('dashboard', [CustomAuthController::class, 'dashboard'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'index'])->name('user.profile');
    Route::post('/profile', [ProfileController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('user.profile.store');

    // Serve l'avatar da storage/app (fuori dalla webroot): {filename} e' il
    // nome generato da hashName(), quindi il pattern esclude a monte
    // path traversal e caratteri fuori da quelli che il framework genera.
    Route::get('/avatars/{filename}', [ProfileController::class, 'avatar'])
        ->where('filename', '[A-Za-z0-9]+\.[A-Za-z0-9]+')
        ->name('user.avatar');

    // Elenchi in sola lettura. Prima erano senza alcun middleware: le view
    // eseguono Auth::user()->isTeacher, quindi un visitatore non autenticato
    // otteneva un errore 500 invece del redirect al login.
    Route::get('/exams', [ExamController::class, 'list'])->name('exam.list');
    Route::get('/libraries', [LibraryQuizController::class, 'list'])->name('libraryquiz.list');

    // Svolgimento dell'esame da parte dello studente.
    Route::get('/exam/{id}', [ExamQuizController::class, 'access'])->name('exam.access');
    Route::post('/exam/sendAnswer', [ExamQuizController::class, 'storeUserAnswers'])
        ->middleware('throttle:10,1')
        ->name('store.user.answer');
});

/*
| Solo docenti
|
| Tutto cio' che crea, modifica, valuta o espone le risposte corrette.
*/
Route::middleware(['auth', 'isTeacher'])->group(function () {

    // --- Quiz ---------------------------------------------------------
    // list e search erano accessibili a ogni utente autenticato, ma la view
    // mostra una colonna "Answer": un qualunque studente poteva sfogliare
    // l'intero archivio delle domande con le risposte corrette.
    Route::get('/quizzes', [QuizController::class, 'list'])->name('quiz.list');
    Route::get('/quizzes/search', [QuizController::class, 'search'])->name('quiz.search');
    Route::get('quiz/create', [QuizController::class, 'create'])->name('quiz.create');
    Route::post('/quizzes', [QuizController::class, 'store'])->name('quiz.store');
    Route::get('/quizzes/{id}/edit', [QuizController::class, 'edit'])->name('quiz.edit');
    Route::put('/quizzes/{id}', [QuizController::class, 'update'])->name('quiz.update');
    Route::delete('/quizzes/{id}', [QuizController::class, 'destroy'])->name('quiz.destroy');

    // --- Librerie -----------------------------------------------------
    Route::get('/library', [LibraryController::class, 'index'])->name('library.library');
    Route::post('/library', [LibraryController::class, 'store'])->name('library.store');
    Route::get('/libraries/{id}/edit', [LibraryController::class, 'edit'])->name('library.edit');
    Route::put('/libraries/{id}', [LibraryController::class, 'update'])->name('library.update');
    Route::delete('/libraries/{id}', [LibraryController::class, 'destroy'])->name('library.destroy');

    // --- Quiz dentro le librerie --------------------------------------
    Route::get('/libraryquiz', [LibraryQuizController::class, 'index'])->name('libraryquiz.index');
    Route::post('/libraryquiz', [LibraryQuizController::class, 'store'])->name('libraryquiz.store');
    // library.quiz mostra le risposte corrette nella colonna "Answer"
    Route::get('/libraryquiz/{id}/quiz', [LibraryQuizController::class, 'quiz_list'])->name('library.quiz');
    Route::post('/libraryquiz/{id}/quiz', [LibraryQuizController::class, 'quiz_list']);
    // La rotta porta l'id della libreria oltre a quello del quiz: senza,
    // il controller non sa da quale libreria scollegare il quiz e finiva per
    // scollegarlo da tutte quelle a cui era associato.
    Route::delete('/libraryquiz/{idlibrary}/quiz/{idquiz}', [LibraryQuizController::class, 'quiz_destroy'])->name('library.quiz.destroy');
    // Endpoint JSON usato dalla pagina "Add Quiz to Exam"
    Route::get('/libraries/{id}/quizzes', [LibraryQuizController::class, 'getQuizzes'])->name('libraries.quiz.exam');

    // --- Esami --------------------------------------------------------
    Route::get('/createExam', [ExamController::class, 'index'])->name('exam.index');
    Route::post('/createExam', [ExamController::class, 'store'])->name('exam.store');
    Route::get('/exams/{id}/edit', [ExamController::class, 'edit'])->name('exam.edit');
    Route::put('/exams/{id}', [ExamController::class, 'update'])->name('exam.update');
    Route::delete('/exams/{id}', [ExamController::class, 'destroy'])->name('exam.destroy');

    // --- Quiz dentro gli esami ----------------------------------------
    Route::get('/examquiz', [ExamQuizController::class, 'index'])->name('examquiz.index');
    Route::post('/examquiz', [ExamQuizController::class, 'store'])->name('examquiz.store');
    Route::get('/examquiz/{id}/quiz', [ExamQuizController::class, 'quiz_list'])->name('exam.quiz');
    Route::post('/examquiz/{id}/quiz', [ExamQuizController::class, 'quiz_list']);
    // Stesso motivo della rotta gemella su library.quiz.destroy: serve l'id
    // dell'esame per scollegare il quiz da quello soltanto.
    Route::delete('/examquiz/{idexam}/quiz/{idquiz}', [ExamQuizController::class, 'quiz_destroy'])->name('exam.quiz.destroy');

    // --- Risultati e valutazione --------------------------------------
    // Erano protette dal solo `auth`: cambiando l'id nell'URL uno studente
    // leggeva, stampava e perfino rivalutava il compito di chiunque altro.
    Route::get('/exam/results/{id}', [ExamQuizController::class, 'indexingResults'])->name('show.users.results.index');
    Route::get('/exam/results/user/{iduser}/{idexam}', [ExamQuizController::class, 'displayUsersAnswer'])->name('display.users.answer');
    // La versione GET permetteva di rivalutare un esame aprendo un link o
    // caricando un'immagine ostile: le GET non passano dal controllo CSRF.
    // La view usa gia' solo la POST gemella qui sotto.
    Route::post('/examcorrect', [ExamQuizController::class, 'correctAnswer'])
        ->middleware('throttle:30,1')
        ->name('display.users.answerP');

    // --- Stampa -------------------------------------------------------
    Route::middleware('throttle:20,1')->group(function () {
        Route::get('/printexam/{idexam}/{iduser}', [ExamQuizController::class, 'printExamUser'])->name('print.exam');
        Route::get('/printexam/{idexam}', [ExamQuizController::class, 'printExam'])->name('print.blankexam');
    });
});
