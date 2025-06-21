<?php

use App\Http\Controllers\TextController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LanguagesController;
use App\Http\Controllers\TranslationsController;
use App\Http\Controllers\OriginalTextsController;
use Illuminate\Support\Facades\Route;

Route::get('/texts/{language}/{text}', [TextController::class, 'index']);
Route::post('/language', [LanguagesController::class, 'store']);
Route::post('/original-text', [OriginalTextsController::class, 'store']);
Route::get('/translator/{language}', [TranslationsController::class, 'index']);
Route::post('/translator', [TranslationsController::class, 'store']);
Route::put('/translator/{translation}', [TranslationsController::class, 'update']);
Route::delete('/translator/{translation}', [TranslationsController::class, 'destroy']);
