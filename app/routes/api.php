<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\IncidentController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


//User routes
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'authenticate'])->name('login');
Route::get('users/delete/{id}', [UserController::class, 'delete']);

Route::get('users', [UserController::class, 'show'])->middleware('auth');



//Project routes
Route::resource('projects', ProjectController::class)->middleware('auth');
Route::controller(ProjectController::class)->middleware('auth')->group(function (){

    Route::post('projects/{project}/users', 'addParticipant');
    Route::get('projects/{project}/users', 'getParticipants');
    Route::get('projects/{project}/managers', 'getManagers');
    Route::delete('projects/{project}/users/{user}', 'removeParticipant');
});


//Activity routes
Route::resource('project/{project}/activities', ActivityController::class)->middleware('auth');


//Incident routes
Route::resource('project/{project}/activity/{activity}/incidents', IncidentController::class);
