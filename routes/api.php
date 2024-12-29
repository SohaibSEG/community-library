<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\LendingController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // Book routes
    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/search', [BookController::class, 'search']);
    Route::get('/books/{id}', [BookController::class, 'show']);
    Route::post('/books', [BookController::class, 'store']);
    Route::put('/books/{id}', [BookController::class, 'update']);
    Route::delete('/books/{id}', [BookController::class, 'destroy']);

    // Lending routes
    Route::post('/books/{bookId}/request', [LendingController::class, 'requestBook']);
    Route::post('/requests/{requestId}/approve', [LendingController::class, 'approveRequest']);
    Route::post('/requests/{requestId}/reject', [LendingController::class, 'rejectRequest']);
    Route::get('/requests/incoming', [LendingController::class, 'getIncomingRequests']);
    Route::get('/requests/outgoing', [LendingController::class, 'getOutgoingRequests']);
    Route::get('/lendings', [LendingController::class, 'getUserLendings']);
    Route::post('/lendings/{lendingId}/return', [LendingController::class, 'setLendingAsReturned']);


    Route::get('/users/books', [BookController::class, 'getUserBooks']);
});


Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});