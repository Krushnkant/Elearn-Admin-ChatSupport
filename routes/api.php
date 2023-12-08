<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/* 
 Author: Ravi Shukla
 Blog: https://w3path.com/
*/

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::group(['namespace' => 'Api\v1', 'prefix' => 'v1', 'v1' => 'v1.'], function () {
    Route::get('categories', 'CategoryController@index');
    
    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
	Route::post('r1', 'AuthController@r1');
    Route::post('update-user', 'AuthController@updateRegister');
    Route::post('otp-verify', 'AuthController@otpVerify');
    Route::post('update-password', 'AuthController@updatePassword'); 
    Route::post('forget-password', 'AuthController@forgetPassword');
    Route::post('resend-otp', 'AuthController@forgetPassword');
    Route::post('recover-password', 'AuthController@updatePassword');
    Route::get('mock-test', 'AssessmentController@mockTest');
    Route::post('post-answer-test', 'AnswerController@store1');
    Route::post('post-answer-test2', 'AnswerController@store2');
});

Route::group(['namespace' => 'Api\v1', 'prefix' => 'v1', 'v1' => 'v1.', 'middleware' => 'auth:api'], function () {
    Route::get('home', 'HomeController@index');
    Route::get('category', 'CategoryController@index');
    Route::get('category-list', 'CategoryController@categorylist');
    Route::resource('courses', 'CourseController')->only(['index', 'show']);
    Route::post('{assessment_id}/questions/{set}', 'QuestionController@index');
    Route::get('{assessment_id}/questions-start-test/{set}', 'QuestionController@startTest');
	Route::post('{assessment_id}/questions-start-test/{set}', 'QuestionController@startTestNew');
    Route::get('{course_id}/assessment', 'AssessmentController@index');
    Route::get('user-profile', 'UserController@index');
    Route::get('e-book', 'QuestionController@ebookList');
    Route::post('post-answer', 'AnswerController@store');
    
    Route::get('{id}/test-results', 'UserController@testResult');
    Route::get('{id}/question-report', 'TestResultController@testResultQuestion');// Question start test report
    Route::post('seen-videos', 'UserController@seenVideos'); // Used for user's seen videos inserted
    Route::post('post-transaction', 'UserController@postTransaction'); // Used for user's seen videos inserted
    Route::post('update-plan', 'UserController@updateplan');
	Route::get('mock-test-test', 'AssessmentController@mockTestTest');
    Route::post('{assessment_id}/questions-set/{set}', 'QuestionController@questionsSetListNew');
	Route::post('{assessment_id}/questions-sets', 'QuestionController@questionsSets');
	Route::post('{assessment_id}/questions-list', 'QuestionController@questionsLists');
    Route::post('buy-course', 'CourseController@buyCoourse');
    Route::get('logout', 'AuthController@logout');

    //Route::post('{chapter_id}/videos-list', 'UserController@VideosList'); 



    //React Project
    //HOME API
    Route::get('course-list', 'HomeController@courseList');
    Route::get('ebook-list', 'HomeController@ebookList');
    Route::get('mocktest-list', 'HomeController@mocktestList');

    //Explore Course
    Route::get('explore', 'HomeController@explore');
    Route::get('explore-course', 'HomeController@exploreCourse');
    Route::get('explore-ebook', 'HomeController@exploreEbook');
  
  


    //E-BOOK LIST API
 //  Route::get('ebook-list', 'EbookConroller@ebookList');
  //  Route::get('ebook-list-name', 'EbookController@ebookListByname');

    //COURSE OVERVIEW API
    Route::get('{id}/courses-overview', 'CourseController@coursesGetById');
    Route::get('{id}/courses-outline', 'CourseController@coursesOutline');

    //CHAPTER VIDEO API
    Route::get('course-video', 'VideoController@videos');
    Route::get('{id}/course-video-detail', 'VideoController@coursevideosbyId');
    Route::get('{id}/video-id', 'VideoController@videosbyId');

    //E BOOK PREVIEW BY COURSE ID
    Route::get('{id}/course-ebook-list', 'CourseController@coursevideosbyId');


    //TOTAL MOCKTEST 
    Route::get('mocktest-total', 'MockTestController@mocktestTotal');
  
});