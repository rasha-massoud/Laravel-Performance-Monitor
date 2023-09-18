<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PagespeedController;
use App\Http\Controllers\ResponseController;

// Getting and Saving the data from the PageSpeed Insight API to the database
Route::get('/api-result', [PagespeedController::class, 'getAPIResult']);
Route::get('/save', [PagespeedController::class, 'saveDataFromAPI']);

// Return the last values from the database neglecting the site condition
Route::post('/response/{column}/{duration}', [ResponseController::class, 'getResult']);

// Updating a column in the database base on id
Route::post('/', [ResponseController::class, 'updateColumn']);

// Get all the data from the DB
Route::get('/response', [ResponseController::class, 'getAll']);
