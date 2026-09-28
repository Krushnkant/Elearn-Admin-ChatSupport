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
    Route::post('post-answer-test2', 'AnswerController@store2');
});

Route::group(['namespace' => 'Api\v1', 'prefix' => 'v1', 'v1' => 'v1.', 'middleware' => 'auth:api'], function () {
    Route::post('post-answer-test', 'AnswerController@store1');

    Route::get('home', 'HomeController@index');
    Route::get('mock-test', 'AssessmentController@mockTest');
    Route::get('category', 'CategoryController@index');
    Route::get('category-list', 'CategoryController@categorylist');
    Route::resource('courses', 'CourseController')->only(['index', 'show']);
    Route::post('{assessment_id}/questions/{set}', 'QuestionController@index');
    Route::get('{assessment_id}/questions-start-test/{set}', 'QuestionController@startTest');
	Route::post('{assessment_id}/questions-start-test/{set}', 'QuestionController@startTestNew');
    Route::get('{course_id}/assessment', 'AssessmentController@index');
    Route::get('user-profile', 'UserController@index');
    Route::post('update-profile', 'UserController@updateProfile');
    Route::get('notification-settings', 'NotificationSettingController@index');
    Route::post('notification-settings', 'NotificationSettingController@update');
    Route::get('search', 'SearchController@index');
    Route::get('notifications', 'NotificationController@index');
    Route::get('notifications-unread-count', 'NotificationController@unreadCount');
    Route::post('notifications-mark-read', 'NotificationController@markRead');
    Route::post('notifications-mark-all-read', 'NotificationController@markAllRead');
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
    Route::get('all-courses', 'CourseController@allCourses');
    Route::get('{id}/course-rating', 'CourseController@rating');
    Route::post('course-rate', 'CourseController@rate');
    Route::get('course-bookmark-ids', 'CourseBookmarkController@ids');
    Route::post('course-bookmark-toggle', 'CourseBookmarkController@toggle');
    Route::get('course-bookmarks', 'CourseBookmarkController@index');
    Route::get('ebook-list', 'HomeController@ebookList');
    Route::get('mocktest-list', 'HomeController@mocktestList');
    Route::get('live-videos', 'LiveVideoController@index');
    Route::get('live-video-seen-ids', 'LiveVideoSeenController@ids');
    Route::post('live-video-seen', 'LiveVideoSeenController@store');
    Route::get('study-materials', 'StudyMaterialController@index');
    Route::get('subscription-plans', 'SubscriptionPlanController@index');
    Route::get('mock-tests', 'PmpMockTestController@index');
    Route::post('contact', 'ContactController@send');
    Route::get('bookmarks', 'BookmarkController@index');
    Route::get('bookmark-ids', 'BookmarkController@ids');
    Route::post('bookmark-toggle', 'BookmarkController@toggle');
    Route::get('lesson-bookmarks', 'LessonBookmarkController@index');
    Route::get('lesson-bookmark-ids', 'LessonBookmarkController@ids');
    Route::post('lesson-bookmark-toggle', 'LessonBookmarkController@toggle');
    Route::get('lesson-notes', 'LessonNoteController@index');
    Route::post('lesson-notes', 'LessonNoteController@store');
    Route::post('lesson-notes-delete', 'LessonNoteController@destroy');
    Route::get('mock-test-builder-options', 'MockTestBuilderController@options');
    Route::get('mock-test-builder-count', 'MockTestBuilderController@count');
    Route::post('mock-test-builder-build', 'MockTestBuilderController@build');
    Route::get('mock-tests/{assessment}/questions', 'PmpExamController@questions');
    Route::get('mock-tests/{assessment}/progress', 'PmpExamController@getProgress');
    Route::post('mock-tests/{assessment}/progress', 'PmpExamController@saveProgress');
    Route::post('mock-tests/{assessment}/submit', 'PmpExamController@submit');
    Route::get('mock-test-results/{result}', 'PmpExamController@result');
    Route::get('mock-test-results/{result}/review', 'PmpExamController@review');
    Route::get('my-progress', 'PmpExamController@myProgress');

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