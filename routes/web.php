<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;

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

use App\Http\Controllers\ApiDocumentationController;

Route::get('/documentation', [ApiDocumentationController::class, 'index'])->name('documentation.index');
Route::get('/documentation/{folder}/{route}', [ApiDocumentationController::class, 'show'])->name('documentation.show');
