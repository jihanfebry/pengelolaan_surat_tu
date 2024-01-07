<?php

use App\Http\Controllers\GuruController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LetterTypeController;
use App\Http\Controllers\LetterController;

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

Route::get('/error-permission', function() {
    return view('errors.permission');
})->name('error.permission');

Route::middleware('IsGuest')->group(function(){
    Route::get('/', function(){
        return view('login'); 
    })->name('login');
    Route::post('/', [UserController::class, 'loginAuth'])->name('login.auth');
});

Route::get('/logout', [UserController::class, 'logout'])->name('logout');



Route::middleware(['IsLogin', 'IsGuru'])->group(function(){
        Route::prefix('/guru')->name('guru.')->group(function(){
        Route::get('/', [GuruController::class, 'index'])->name('index');
     });

});


Route::middleware(['IsLogin', 'IsStaff'])->group(function(){
    Route::prefix('/user/guru')->name('user.guru.')->group(function () {
        Route::get('/data', [UserController::class, 'getDataGuru'])->name('data');
        Route::get('/create', [UserController::class, 'createGuru'])->name('create');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/search', [UserController::class, 'searchGuru'])->name('search');
        Route::get('/edit{id}', [UserController::class, 'edit'])->name('edit');
        Route::patch('/update{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/delete{id}', [UserController::class, 'destroy'])->name('delete');
    });
    Route::prefix('/user/staff')->name('user.staff.')->group(function () {
        Route::get('/data', [UserController::class, 'getDataStaff'])->name('data');
        Route::get('/create', [UserController::class, 'createStaff'])->name('create');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/search', [UserController::class, 'searchStaff'])->name('search');
        Route::get('/edit{id}', [UserController::class, 'edit'])->name('edit');
        Route::patch('/update{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/delete{id}', [UserController::class, 'destroy'])->name('delete');
    });

    Route::prefix('/klasifikasi')->name('klasifikasi.')->group(function() {
        Route::get('/data', [LetterTypeController::class, 'getClassificate'])->name('data');
        Route::post('/store', [LetterTypeController::class, 'store'])->name('store');
        Route::get('/create', [LetterTypeController::class, 'createClassificate'])->name('create');
        Route::get('/search', [LetterTypeController::class, 'searchClassificate'])->name('search');
        Route::get('/detail{letter_code}', [LetterTypeController::class, 'show'])->name('detail');
        Route::get('/edit{id}', [LetterTypeController::class, 'edit'])->name('edit');
        Route::patch('/update{id}', [LetterTypeController::class, 'update'])->name('update');
        Route::delete('/delete{id}', [LetterTypeController::class, 'destroy'])->name('delete');
        Route::get('/download-excel', [letterTypeController::class, 'downloadExcel'])->name('download-excel');
    });

    Route::prefix('/dataSurat')->name('dataSurat.')->group(function() {
        Route::get('/data', [LetterController::class, 'getLetters'])->name('data');
        Route::get('/create', [LetterController::class, 'createLetters'])->name('create');
        Route::post(' /store', [LetterController::class, 'store'])->name('store');
        Route::get('/search', [LetterController::class, 'searchLetters'])->name('search'); 
        Route::get('/trix', 'TrixController@index');
        Route::post('/upload', 'TrixController@upload');
        Route::post('/store', 'TrixController@store');
        Route::get('/edit{id}', [LetterController::class, 'edit'])->name('edit');
        Route::get('/show{id}', [LetterController::class, 'show'])->name('show');
        Route::patch('/update{id}', [LetterController::class, 'update'])->name('update');
        Route::delete('/delete{id}', [LetterController::class, 'destroy'])->name('delete');
        Route::get('/download-excel', [letterController::class, 'downloadExcel'])->name('download-excel');
        Route::get('/download-pdf{id}',[LetterController::class, 'downloadPDF'])->name('download-pdf');
    });
 
});


    

Route::middleware(['IsLogin'])->group(function (){
    Route::get('/logout', [UserController::class, 'logout'])->name('logout');
    Route::get('/home', function(){
        return view('home');
    })->name('home.page');
});