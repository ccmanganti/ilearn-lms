<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use \App\Models\Uclasses;
use Illuminate\Http\Request;
use \App\Models\Sample;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// JOIN CLASSROOM
Route::post('/joinclass', [Controller::class, 'joinClass'])->name('joinclass');
Route::post('/submitassess', [Controller::class, 'submitAssess'])->name('submitassess');
Route::post('/selectstudent', [Controller::class, 'selectStudent'])->name('selectstudent');
Route::post('/submitscore', [Controller::class, 'submitScore'])->name('submitscore');
Route::post('/unenrollclass', [Controller::class, 'submitUnenroll'])->name('unenrollclass');

// UPLAD SUBMIT
Route::post('/submit', [Controller::class, 'store'])->name('submit');

Route::delete('/lms/assignments/{code}/tmp-delete', [Controller::class, 'tmpDelete'])->name('tmp-delete');
Route::post('/lms/assignments/{code}/tmp-upload', [Controller::class, 'tmpUpload'])->name('tmp-upload');

// Use dynamic AJAX call to change page contents. 
// For every page change, use form in order to pass data to 
// identify what form of change the page will take.


Route::get('/artisan/linkstorage', function () {
    Artisan::call('storage:link');
});

Route::get('/artisan/resetresources', function () {
    Artisan::call('migrate:refresh --path=/database/migrations/2023_06_10_062738_create_classes_table.php');
    Artisan::call('migrate:refresh --path=/database/migrations/2023_06_10_081745_create_posts_table.php');
    Artisan::call('migrate:refresh --path=/database/migrations/2023_06_13_062913_create_students_table.php');
    Artisan::call('migrate:refresh --path=/database/migrations/2023_06_12_040434_create_assessments_table.php');
    Artisan::call('migrate:refresh --path=/database/migrations/2023_06_10_103452_create_tasks_table.php');
});

