<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [App\Http\Controllers\APIController::class, 'index'])->name('Index');
Route::post('/', [App\Http\Controllers\APIController::class, 'PostWatchedMovieView']);
Route::get('/watched_movie', [App\Http\Controllers\APIController::class, 'RoutingFunc'])->name('move_watched_movie');
Route::post('/watched_movie', [App\Http\Controllers\APIController::class, 'RoutingFunc']);
Route::get('/watched_movie/rate', [App\Http\Controllers\APIController::class, 'Sort_rate'])->name('sort_rate');
Route::get('/watched_movie/updated', [App\Http\Controllers\APIController::class, 'Sort_updated'])->name('sort_updated');

Route::get('/logout', [App\Http\Controllers\LogOutController::class, 'loggedOut'])->name('loggedOutRoute');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Auth::routes();



