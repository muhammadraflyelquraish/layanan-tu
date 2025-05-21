<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SPJCategoryController;
use App\Http\Controllers\SPJController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/role/data', [RoleController::class, 'data'])->name('role.data');
    Route::resource('/role', RoleController::class);
    Route::get('/user/data', [UserController::class, 'data'])->name('user.data');
    Route::resource('/user', UserController::class);

    // Proposal
    Route::get('/letter/data', [LetterController::class, 'data'])->name('letter.data');
    Route::put('/letter/{letter}/disposition', [LetterController::class, 'disposition'])->name('letter.disposition');
    Route::put('/letter/{letter}/confirmation', [LetterController::class, 'confirmation'])->name('letter.confirmation');
    Route::get('/letter/{letter}/spj', [LetterController::class, 'spj'])->name('letter.spj');
    Route::resource('/letter', LetterController::class);

    // SPJ Category
    Route::post('/spj-category', [SPJCategoryController::class, 'store'])->name('spj.category.store');

    // SPJ
    Route::get('/spj/data', [SPJController::class, 'data'])->name('spj.data');
    Route::get('/spj/{spj}/approval', [SPJController::class, 'approvalView'])->name('spj.approval.view');
    Route::put('/spj/{spj}/revisi', [SPJController::class, 'revisi'])->name('spj.revisi');
    Route::resource('/spj', SPJController::class);

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
