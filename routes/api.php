<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get(
    '/initial-scrapping',
    [\App\Http\Controllers\VirtualPhoneController::class, 'initialScrap']
)->name('api.initial.scrapping');

Route::get(
    '/virtual-phone/countries',
    [\App\Http\Controllers\VirtualPhoneController::class, 'getCountries']
)->name('api.get.countries');

Route::get(
    '/virtual-phone/numbers',
    [\App\Http\Controllers\VirtualPhoneController::class, 'getNumbers']
)->name('api.get.numbers');

Route::get(
    '/virtual-phone/sms-history',
    [\App\Http\Controllers\VirtualPhoneController::class, 'smsHistory']
)->name('api.get.sms.history');
