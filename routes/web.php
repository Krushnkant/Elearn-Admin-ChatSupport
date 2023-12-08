<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\AjaxController;


/* 
 Author: Ravi Shukla
 Blog: https://w3path.com/
*/

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/clear-cache', function() {
    //dd(bcrypt('123456'));
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    return "Cache is cleared";
});

Route::get('/', function () {
    return view('welcome');
});

/*Route::get('/admin', function () {
    return Redirect::to("login");
})->name('login');*/

Route::get('/admin', function () {
    //return Redirect::to("admin/login");
    return view('auth.login');
})->name('login');

/*Route::get('/admin/login', [Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::class, 'create'])->name('login');
Route::get('/', [Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::class, 'create'])->name('login');
*/


Route::any('/import-csv', 'Excel@downloadFile');
Route::get('/profile', function () {
    // Only verified users may access this route...
})->middleware('verified');


Route::middleware(['auth:sanctum', 'verified'])->get('/admin/dashboard', function () {
    return Inertia\Inertia::render('Dashboard');
})->name('admin.dashboard');



   
 Route::namespace('Admin')->prefix('admin')->as('admin.')->middleware('auth')->group(function(){
    //Route::get('login', 'AuthController@login')->name('login');
    Route::any('change-status', 'AjaxController@changeStatus');
    Route::post('check-email', 'AjaxController@mksCheckEmail');
    Route::post('check-mobile', 'AjaxController@mksCheckMobile');
    Route::post('hard-delete', 'AjaxController@hardDelete');
    Route::get('change-password','AuthController@getUpdatePassword')->name('change-password');
    Route::post('change-password','AuthController@postUpdatePassword');
    Route::get('dashboard','HomeController@index')->name('admin.dashboard');
    Route::get('category',[CategoryController::class, 'index']);
    Route::get('category/create',[CategoryController::class, 'create']);
    Route::post('category/store',[CategoryController::class, 'store']);
    Route::get('category/{id}/edit',[CategoryController::class, 'edit']);
    Route::post('category/update',[CategoryController::class, 'update']);
    //Route::post('hard-delete',[CategoryController::class, 'hardDelete']);
    Route::get('log-out',[CategoryController::class, 'logout']);

    /*Route::group(['prefix' => 'questions'], function () {
        Route::get('/','QuestionController@index');
        Route::get('/create','QuestionController@create');
        Route::get('/import-csv','QuestionController@getImportCSV');
        Route::post('/import-csv','QuestionController@postImportCSV');
        Route::post('/store','QuestionController@store');
        Route::get('/{id}/edit','QuestionController@edit');
        Route::post('/update','QuestionController@update');
        Route::post('hard-delete', 'QuestionController@harDelete');
    });*/

    // Route For Users List
    Route::group(['prefix' => 'users'], function () {
        Route::get('/','UserController@index');
        Route::get('/create','UserController@create');
        Route::post('/store','UserController@store');
        Route::get('/{id}/edit','UserController@edit');
        Route::post('/update','UserController@update');
    });

    // Route For Users Result List
    Route::get('users/{course_id}/results', 'UserResultController@index');
    Route::get('users/{course_id}/results/create', 'UserResultController@create');
    Route::get('users/{course_id}/results/{id}/edit','UserResultController@edit');
    Route::post('/store','UserResultController@store');
    Route::post('/update','UserResultController@update');

    Route::get('repors/','UserController@report');
    Route::get('repors/{id}/test-results', 'UserController@testResultgraf');

    // Route For Course List
    Route::group(['prefix' => 'courses'], function () {
        Route::get('/','CourseController@index');
        Route::get('/create','CourseController@create');
        Route::post('/store','CourseController@store');
        Route::get('/{id}/edit','CourseController@edit');
        Route::post('/update','CourseController@update');
    });

    // For books Route
    Route::get('courses/{course_id}/books', 'BookController@index');
    Route::get('courses/{course_id}/books/create', 'BookController@create');
    Route::get('courses/{course_id}/books/{id}/edit','BookController@edit');
    Route::post('books/store', 'BookController@store');
    Route::post('books/update', 'BookController@update');


    // For Chapters Route
    Route::get('courses/{course_id}/chapters', 'ChapterController@index');
    Route::get('courses/{course_id}/chapters/create', 'ChapterController@create');
    Route::get('courses/{course_id}/chapters/{id}/edit','ChapterController@edit');
    Route::post('chapters/store', 'ChapterController@store');
    Route::post('chapters/update', 'ChapterController@update');

    // For Questions Route
    Route::get('courses/{course_id}/assessments', 'AssessmentController@index');
    Route::get('courses/{course_id}/assessments/create', 'AssessmentController@create');
    Route::get('courses/{course_id}/assessments/{id}/edit','AssessmentController@edit');
    Route::post('assessments/store', 'AssessmentController@store');
    Route::post('assessments/update', 'AssessmentController@update');

    // For Questions Route
    Route::get('assessments/{assessment_id}/questions', 'QuestionController@index');
    Route::get('assessments/{assessment_id}/questions/create', 'QuestionController@create');
    Route::get('assessments/{assessment_id}/questions/{id}/edit','QuestionController@edit');
    Route::get('assessments/{assessment_id}/import-csv','QuestionController@getImportCSV');
    Route::post('questions/import-csv','QuestionController@postImportCSV');
    Route::post('questions/store', 'QuestionController@store');
    Route::post('questions/update', 'QuestionController@update');
    Route::post('question-delete', 'QuestionController@hardDelete');
    

    // For Videos Route
    Route::get('chapters/{chapter_id}/videos', 'VideoController@index');
    Route::get('chapters/{chapter_id}/videos/create', 'VideoController@create');
    Route::get('chapters/{chapter_id}/videos/{id}/edit','VideoController@edit');
    Route::post('videos/store', 'VideoController@store');
    Route::post('videos/update', 'VideoController@update');
    /*Route::group(['prefix' => 'courses/'.{$id}.'/chapters'], function () {
        Route::get('/','ChapterController@index');
        Route::get('/create','ChapterController@create');
        Route::post('/store','ChapterController@store');
        Route::get('/{id}/edit','ChapterController@edit');
        Route::post('/update','ChapterController@update');
    });*/

    // Route For Ebook List
    Route::group(['prefix' => 'ebooks'], function () {
        Route::get('/','EBookController@index');
        Route::get('/create','EBookController@create');
        Route::post('/store','EBookController@store');
        Route::get('/{id}/edit','EBookController@edit');
        Route::post('/update','EBookController@update');
    });
});
