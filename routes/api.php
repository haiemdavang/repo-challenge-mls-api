<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\CheckTeacherRole;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/refresh', [AuthController::class, 'refreshToken']);
Route::get('/categories', [CategoryController::class, 'index']);

Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{id}', [CourseController::class, 'show']);
Route::get('/courses/{id}/modules', [ModuleController::class, 'index']);
Route::get('/modules/{id}', [ModuleController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // User Profile
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);

    // Dashboard APIs
    Route::get('/my-courses', [DashboardController::class, 'myCourses']);
    Route::get('/schedule', [DashboardController::class, 'schedule']);

    // Teacher APIs
    Route::middleware('teacher_role')->group(function () {
        Route::post('/courses', [CourseController::class, 'store']);
        Route::put('/courses/{id}', [CourseController::class, 'update']);
        Route::post('/courses/{id}/enroll', [CourseController::class, 'enroll']);
        Route::get('/courses/{id}/students', [CourseController::class, 'students']);

        Route::post('/courses/{id}/modules', [ModuleController::class, 'store']);
        Route::put('/modules/{id}', [ModuleController::class, 'update']);
        Route::delete('/modules/{id}', [ModuleController::class, 'destroy']);
    });
});
