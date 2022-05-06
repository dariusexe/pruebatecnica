<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\UserController;

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
    return view('welcome');
});

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'authenticate']);
Route::get('users/delete/{id}', [UserController::class, 'delete']);

Route::resource('projects', ProjectController::class)->middleware('jwt.verify');
Route::post('projects/{project}/users', [ProjectController::class, 'addParticipant'])->middleware('jwt.verify');
Route::get('projects/{project}/users', [ProjectController::class, 'getParticipants'])->middleware('jwt.verify');
Route::delete('projects/{project}/users/{user}', [ProjectController::class, 'removeParticipant'])->middleware('jwt.verify');
Route::resource('activities', ActivityController::class);
Route::resource('incidents', IncidentController::class);
