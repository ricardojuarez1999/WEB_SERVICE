<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ViewController;
use App\Http\Controllers\ViewDashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\ChatController;
use \App\Http\Controllers\MessageController;
use \App\Http\Controllers\AmigoController;

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

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class,'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('admin', [AuthController::class,'isAdministrador']);
});

Route::group([
    'prefix'=>'user'
],function(){
    Route::post('/profile', [UserController::class,'updateProfile']);
    Route::get('/profile', [UserController::class,'getProfile']);
    Route::get('/profile/{iduser}', [UserController::class,'x']);
});

Route::group([
    'prefix' => 'post'
],function(){
    Route::post('/create', [PostController::class, 'create']);
    Route::post('/delete/{id}', [PostController::class, 'delete']); // TODO: Agregar esta parte
    Route::get('/getPost/{id}', [PostController::class, 'getPostId']);
    Route::get('/getPostsFriends',[PostController::class,'getPostsFriends']);
    Route::get('/getPostsFriends/{start}/{end}',[PostController::class,'getPostsFriendsRange']);
});

Route::group([
    'prefix'=> 'blog'
],function(){
    Route::get('/get/{start}/{end}', [BlogController::class, 'getBlogPostRange']);
    Route::get('/get/{id}/{start}/{end}', [BlogController::class, 'getBlogPostRangeUseridentity']);
    Route::get('/getAll', [BlogController::class, 'getBlogPostAll']);
    Route::get('/getAll/{id}', [BlogController::class, 'getBlogPostAllUseridentity']);
});

Route::group([
    'prefix'=>'search'
],function(){
    Route::get('/{data}', [UserController::class, 'search']);
    Route::get('/{data}/{start}/{end}', [UserController::class, 'searchRange']);
});

Route::group([
    'prefix'=>'chat'
],function(){
    Route::post('new', [ChatController::class, 'newChat']);
    Route::put('accept/{id}', [ChatController::class, 'acceptChat']);
    Route::get('get', [ChatController::class, 'getAllChats']);
    Route::get('get/{start}/{end}', [ChatController::class, 'getChatsRange']);
    Route::get('getPending', [ChatController::class, 'getPendingChats']);
    //add date to chat, and order by date (get's)
});

Route::group([
    'prefix'=>'messages'
],function(){
    Route::post('new', [MessageController::class, 'newMessage']);
    Route::get('get/{id}', [MessageController::class, 'getAllMessages']);
    Route::get('get/{id}/{start}/{end}', [MessageController::class, 'getMessageRange']);
});

Route::group([
    'prefix'=>'friends'
],function(){
    Route::post('new', [AmigoController::class, 'newFriend']);
    Route::put('accept/{id}', [AmigoController::class, 'acceptFriend']);
    Route::get('get', [AmigoController::class, 'getAllFriends']);
    Route::get('get/{start}/{end}', [AmigoController::class, 'getFriendsRange']);
    Route::get('getPending', [AmigoController::class, 'getPendingFriends']);
});

Route::group([
   'prefix'=>'view'
],function(){
    Route::get('/newView/{useridentity}', [ViewController::class, 'newView']);
    Route::get('/topView', [ViewController::class, 'getTopViews']);
});

Route::group([
    'prefix'=>'dashboard'
],function(){
    Route::get('/newViewPage/{page}', [ViewDashboardController::class, 'newViewPage']);
    Route::get('/getViewPages', [ViewDashboardController::class, 'getViewPages']);
});
