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
Route::get('/quizzes', [App\Http\Controllers\QuizController::class, 'list'])->name('quiz.list')->middleware(['auth', 'isTeacher']);
Route::get('/quizzes', [App\Http\Controllers\QuizController::class, 'index'])->name('quiz.index')->middleware(['auth', 'isTeacher']);
Route::get('/quizzes/search', [App\Http\Controllers\QuizController::class, 'list'])->name('quiz.search')->middleware(['auth', 'isTeacher']);

Route::delete('/quizzes/{id}', [App\Http\Controllers\QuizController::class, 'destroy'])->name('quiz.destroy')->middleware(['auth', 'isTeacher']);
//rotte quiz - Creazione e store
Route::get('quiz/create', [App\Http\Controllers\QuizController::class, 'create'])->name('quiz.create')->middleware(['auth', 'isTeacher']);
Route::post('/quizzes', [App\Http\Controllers\QuizController::class, 'store'])->name('quiz.store')->middleware(['auth', 'isTeacher']);

Route::get('/quizzes/{id}/edit', [App\Http\Controllers\QuizController::class, 'edit'])->name('quiz.edit')->middleware(['auth', 'isTeacher']);
Route::put('/quizzes/{id}', [App\Http\Controllers\QuizController::class, 'update'])->name('quiz.update')->middleware(['auth', 'isTeacher']);
