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

Route::get('dashboard', [CustomAuthController::class, 'dashboard'])->name('dashboard');
Route::get('login', [CustomAuthController::class, 'index'])->name('login');
Route::post('custom-login', [CustomAuthController::class, 'customLogin'])->name('login.custom');
Route::get('register', [CustomAuthController::class, 'registration'])->name('register');
Route::post('custom-registration', [CustomAuthController::class, 'customRegistration'])->name('register.custom');
Route::get('signout', [CustomAuthController::class, 'signOut'])->name('signout');
Route::get('login', [CustomAuthController::class, 'showLoginForm'])->name('login');
Route::get('register', [CustomAuthController::class, 'showRegisterForm'])->name('register');
Route::get('/', [CustomAuthController::class, 'showHome'])->name('/');
Route::get('dashboard', [CustomAuthController::class, 'showHome2'])->name('dashboard');

//Auth::routes();
//rotte gestione user
Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('user.profile');
Route::post('/profile', [App\Http\Controllers\ProfileController::class, 'store'])->name('user.profile.store');

//rotte quiz
Route::get('/quizzes', [App\Http\Controllers\QuizController::class, 'index'])->name('quiz.index');
Route::post('/quizzes', [App\Http\Controllers\QuizController::class, 'store'])->name('quiz.store');
