<?php

use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CustomerCategoryController;
use App\Http\Controllers\Api\CustomerController;
use Illuminate\Support\Facades\Route;

Route::get('/customer-categories', [CustomerCategoryController::class, 'index']);

Route::apiResource('customers', CustomerController::class);

Route::get('/customers/{customer}/contacts', [ContactController::class, 'index']);
Route::apiResource('contacts', ContactController::class);

