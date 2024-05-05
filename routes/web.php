<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TaskController::class,"index"])->name("app.home");
Route::post('/add_task', [TaskController::class,"addTask"])->name("add_task");
Route::post('/edit_task/{id?}', [TaskController::class,"editTask"])->name("edit_task");
Route::match(["get","post"],"/filter_tasks",[TaskController::class,"index"])->name("filter_tasks");
Route::delete('/delete_task/{id?}', [TaskController::class,"deleteTask"])->name("delete_task");
Route::delete('/delete_all_tasks', [TaskController::class, 'deleteAllTasks'])->name('delete_all_tasks');
Route::post('/update_task_order', [TaskController::class, 'updateTaskOrder'])->name('update_task_order');


