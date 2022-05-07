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
//User routes
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'authenticate'])->name('login');
Route::get('users/delete/{id}', [UserController::class, 'delete']);

Route::get('users', [UserController::class, 'show'])->middleware('auth');


//Project routes
Route::resource('projects', ProjectController::class)->middleware('auth');
Route::controller(ProjectController::class)->group(function (){

    Route::post('projects/{project}/users', 'addParticipant');
    Route::get('projects/{project}/users', 'getParticipants');
    Route::get('projects/{project}/managers', 'getManagers');
    Route::delete('projects/{project}/users/{user}', 'removeParticipant');
});


//Activity routes
Route::resource('project/{project}/activities', ActivityController::class);


//Incident routes
Route::resource('project/{project}/activity/{activity}/incidents', IncidentController::class);
