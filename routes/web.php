<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomAuthController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('dashboard', [CustomAuthController::class, 'dashboard'])->name('dashboard')->middleware('auth');;
Route::get('login', [CustomAuthController::class, 'index'])->name('login')->middleware('guest');
Route::post('custom-login', [CustomAuthController::class, 'customLogin'])->name('login.custom')->middleware('guest');
Route::get('register', [CustomAuthController::class, 'registration'])->name('register')->middleware('guest');
Route::post('custom-registration', [CustomAuthController::class, 'customRegistration'])->name('register.custom')->middleware('guest');
Route::post('signout', [CustomAuthController::class, 'signOut'])->name('signout')->middleware('auth');

//Auth::routes();
//rotte gestione user
Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('user.profile')->middleware('auth');
Route::post('/profile', [App\Http\Controllers\ProfileController::class, 'store'])->name('user.profile.store')->middleware('auth');

//rotte quiz - Dove mostra i vari quiz e possono essere modificati o rimossi
Route::get('/quizzes', [App\Http\Controllers\QuizController::class, 'list'])->name('quiz.list')->middleware('auth');
Route::get('/quizzes', [App\Http\Controllers\QuizController::class, 'index'])->name('quiz.index')->middleware('auth');
Route::get('/quizzes/search', [App\Http\Controllers\QuizController::class, 'search'])->name('quiz.search')->middleware('auth');

Route::delete('/quizzes/{id}', [App\Http\Controllers\QuizController::class, 'destroy'])->name('quiz.destroy')->middleware(['auth', 'isTeacher']);
//rotte quiz - Creazione e store
Route::get('quiz/create', [App\Http\Controllers\QuizController::class, 'create'])->name('quiz.create')->middleware(['auth', 'isTeacher']);
Route::post('/quizzes', [App\Http\Controllers\QuizController::class, 'store'])->name('quiz.store')->middleware(['auth', 'isTeacher']);

Route::get('/quizzes/{id}/edit', [App\Http\Controllers\QuizController::class, 'edit'])->name('quiz.edit')->middleware(['auth', 'isTeacher']);
Route::put('/quizzes/{id}', [App\Http\Controllers\QuizController::class, 'update'])->name('quiz.update')->middleware(['auth', 'isTeacher']);

//rotte gestione librerie->middleware(['auth', 'isTeacher'])
Route::get('/library', [App\Http\Controllers\LibraryController::class, 'index'])->name('library.library')->middleware(['auth', 'isTeacher']);
Route::post('/library', [App\Http\Controllers\LibraryController::class, 'store'])->name('library.store')->middleware(['auth', 'isTeacher']);

//rotte per gestione aggiunta di quiz ad una libreria
Route::get('/libraryquiz', [App\Http\Controllers\LibraryQuizController::class, 'index'])->name('libraryquiz.index')->middleware(['auth', 'isTeacher']);
Route::post('/libraryquiz', [App\Http\Controllers\LibraryQuizController::class, 'store'])->name('libraryquiz.store')->middleware(['auth', 'isTeacher']); // Cambiato da 'addQuiz' a 'store'
Route::post('/libraryquiz/{library_id}/{quiz_id}', [App\Http\Controllers\LibraryQuizController::class, 'store'])->name('libraryquiz.addg')->middleware(['auth', 'isTeacher']);


Route::get('/libraries', [App\Http\Controllers\LibraryQuizController::class, 'list'])->name('libraryquiz.list');//questa andrrebbe in libraryController in realtà
//rotte per la gestione dei comandi Library List (delete, quiz, edit)
Route::delete('/libraries/{id}', [App\Http\Controllers\LibraryController::class, 'destroy'])->name('library.destroy')->middleware(['auth', 'isTeacher']);
Route::get('/libraries/{id}/edit', [App\Http\Controllers\LibraryController::class, 'edit'])->name('library.edit')->middleware(['auth', 'isTeacher']);
Route::put('/libraries/{id}', [App\Http\Controllers\LibraryController::class, 'update'])->name('library.update');

Route::get('/libraryquiz/{id}/quiz', [App\Http\Controllers\LibraryQuizController::class, 'quiz_list'])->name('library.quiz');
Route::post('/libraryquiz/{id}/quiz', [App\Http\Controllers\LibraryQuizController::class, 'quiz_list'])->name('library.quiz');
Route::delete('/libraryquiz/delete/{id}', [App\Http\Controllers\LibraryQuizController::class, 'quiz_destroy'])->name('library.quiz.destroy');

//rotte per gli esami
Route::get('/exams', [App\Http\Controllers\ExamController::class, 'list'])->name('exam.list');//questa andrrebbe in libraryController in realtà

Route::post('/createExam', [App\Http\Controllers\ExamController::class, 'store'])->name('exam.store')->middleware(['auth', 'isTeacher']);
Route::get('/createExam', [App\Http\Controllers\ExamController::class, 'index'])->name('exam.index')->middleware(['auth', 'isTeacher']);

//rotte per gestione aggiunta di quiz ad un esame
Route::get('/examquiz', [App\Http\Controllers\ExamQuizController::class, 'index'])->name('examquiz.index')->middleware(['auth', 'isTeacher']);
Route::post('/examquiz', [App\Http\Controllers\ExamQuizController::class, 'store'])->name('examquiz.store')->middleware(['auth', 'isTeacher']); // Cambiato da 'addQuiz' a 'store'
//Route::post('/examquiz/{exam_id}/{quiz_id}', [App\Http\Controllers\ExamQuizController::class, 'storeg'])->name('examquiz.addg')->middleware(['auth', 'isTeacher']);

//Rotte per la gestione delle action di exam
Route::delete('/exams/{id}', [App\Http\Controllers\ExamController::class, 'destroy'])->name('exam.destroy');
Route::get('/exams/{id}/edit', [App\Http\Controllers\ExamController::class, 'edit'])->name('exam.edit');
Route::put('/exams/{id}', [App\Http\Controllers\ExamController::class, 'update'])->name('exam.update');
//route action quiz for exam
Route::get('/examquiz/{id}/quiz', [App\Http\Controllers\ExamQuizController::class, 'quiz_list'])->name('exam.quiz');
Route::post('/examquiz/{id}/quiz', [App\Http\Controllers\ExamQuizController::class, 'quiz_list'])->name('exam.quiz');
Route::delete('/examquiz/delete/{id}', [App\Http\Controllers\ExamQuizController::class, 'quiz_destroy'])->name('exam.quiz.destroy');
//ROTTA PER PERMETTERE IL FETCH DEI QUIZ ALL'AJAX
Route::get('/libraries/{id}/quizzes', [App\Http\Controllers\LibraryQuizController::class, 'getQuizzes'])->name('libraries.quiz.exam');

Route::get('/exam/{id}', [App\Http\Controllers\ExamQuizController::class, 'access'])->name('exam.access')->middleware('auth');
Route::post('/exam/sendAnswer', [App\Http\Controllers\ExamQuizController::class, 'storeUserAnswers'])->name('store.user.answer');

Route::get('/exam/results/{id}', [App\Http\Controllers\ExamQuizController::class, 'indexingResults'])->name('show.users.results.index');

Route::get('/exam/results/user/{iduser}/{idexam}', [App\Http\Controllers\ExamQuizController::class, 'displayUsersAnswer'])->name('display.users.answer');

