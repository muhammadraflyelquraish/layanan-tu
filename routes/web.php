<?php

use App\Http\Controllers\ArsipController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisposisiController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\LabelSPJController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QRController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SPJCategoryController;
use App\Http\Controllers\SPJController;
use App\Http\Controllers\TrackingController;
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

Route::get('login-qr', [QRController::class, 'generate'])->name('qr.login');
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/role/data', [RoleController::class, 'data'])->name('role.data');
    Route::resource('/role', RoleController::class);
    Route::get('/user/data', [UserController::class, 'data'])->name('user.data');
    Route::resource('/user', UserController::class);

    // Proposal
    Route::get('/letter/data', [LetterController::class, 'data'])->name('letter.data');
    Route::post('/letter/{letter}/disposition', [LetterController::class, 'disposition'])->name('letter.disposition');
    Route::get('/letter/{letter}/target-disposition', [LetterController::class, 'targetDisposition'])->name('letter.target.disposition');
    Route::put('/letter/{letter}/confirmation', [LetterController::class, 'confirmation'])->name('letter.confirmation');
    Route::get('/letter/{letter}/spj', [LetterController::class, 'spj'])->name('letter.spj');
    Route::resource('/letter', LetterController::class);

    // SPJ Category
    Route::post('/spj-category', [SPJCategoryController::class, 'store'])->name('spj.category.store');

    // SPJ
    Route::get('/spj/data', [SPJController::class, 'data'])->name('spj.data');
    Route::get('/spj/{spj}/approval', [SPJController::class, 'approvalView'])->name('spj.approval.view');
    Route::put('/spj/{spj}/revisi', [SPJController::class, 'revisi'])->name('spj.revisi');
    Route::get('/spj/{spj}/rating', [SPJController::class, 'getRating'])->name('spj.get.rating');
    Route::post('/spj/rating', [SPJController::class, 'rating'])->name('spj.rating');
    Route::resource('/spj', SPJController::class);

    // Label SPJ
    Route::get('/label-spj/data', [LabelSPJController::class, 'data'])->name('label-spj.data');
    Route::resource('/label-spj', LabelSPJController::class);

    // Tracking
    Route::get('/tracking/data', [TrackingController::class, 'data'])->name('tracking.data');
    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');

    // Disposisi
    Route::get('/disposisi/data', [DisposisiController::class, 'data'])->name('disposisi.data');
    Route::resource('/disposisi', DisposisiController::class);

    // Arsip
    Route::get('/arsip/data', [ArsipController::class, 'data'])->name('arsip.data');
    Route::resource('/arsip', ArsipController::class);

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
