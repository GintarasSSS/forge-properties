<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertySearchController;

Route::get('/', [PropertySearchController::class, 'index'])->name('property.search');
