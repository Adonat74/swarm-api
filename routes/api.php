<?php

use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\Admin\AdminGroupController;
use App\Http\Controllers\Admin\AdminMessageController;
use App\Http\Controllers\Admin\AdminUserController;
use Illuminate\Support\Facades\Route;

///////////////////////////////////////////////////////////////////////////////////
///////////////////////////////// USER ROUTES /////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////


/////////////////////////////// COMMENT /////////////////////////////////////////
Route::controller(CommentController::class)->group(function () {
    Route::get('events/{event}/comments', 'getEventComments'); // return the number of replies if any
    Route::get('comments/{comment}/replies', 'getCommentReplies');
    Route::post('events/{event}/comments', 'addEventComment');
    Route::post('comments/{comment}/replies', 'addCommentReply');
    Route::post('comments/{comment}/likes', 'addCommentLike');
    Route::delete('comments/{comment}/likes', 'deleteCommentLike');
    Route::post('comments/{comment}', 'updateComment');
    Route::delete('comments/{comment}', 'deleteComment');
});

////////////////////////////////// MESSAGE ////////////////////////////////////////
Route::controller(MessageController::class)->group(function () {
    Route::get('groups/{group}/messages', 'getGroupMessages');
    Route::post('groups/{group}/messages', 'addGroupMessage');
    Route::post('messages/{message}', 'updateMessage');
    Route::delete('messages/{message}', 'deleteMessage');
});

/////////////////////////////// EVENT /////////////////////////////////////////
Route::prefix('events')->controller(EventController::class)->group(function () {
    Route::get('/{event}', 'getEvent');
    Route::get('/{event}/users', 'getEventUsers');
    Route::get('/{event}/comments', 'getEventComments');
    Route::get('/{event}/images', 'getEventImages');
    Route::post('/', 'addEvent');
    Route::post('/{event}/participate', 'joinEvent');
    Route::delete('/{event}/participate', 'leaveEvent');
});

/////////////////////////////// GROUP /////////////////////////////////////////
Route::prefix('groups')->controller(GroupController::class)->group(function () {
    Route::get('/{group}', 'getGroup');
    Route::get('/{group}/events', 'getGroupEvents');
    Route::get('/{group}/users', 'getGroupUsers');
    Route::get('/{group}/images', 'getGroupImages');
    Route::post('/', 'addGroup');
    Route::post('/{group}/status', 'updateGroupUserStatus');
});

////////////////////////////////// USER ////////////////////////////////////////////////
Route::prefix('user')->controller(UserController::class)->group(function () {
    Route::get('/', 'getUser');
    Route::get('/groups', 'getUserGroups'); // eventually bind incoming events for the user groups interface incomming events number preview
    Route::get('/events', 'getUserEvents');
    Route::post('/', 'updateUser');
    Route::delete('/', 'deleteUser');
});

//////////////////////////////// AUTHENTICATION ///////////////////////////////////////
Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::post('/logout', 'logout');
    Route::get('/refresh', 'refresh');
});


///////////////////////////////////////////////////////////////////////////////////
//////////////////////////////// CREATOR ROUTES ///////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////
Route::prefix('creator')->group(function () {

    /////////////////////////////// CREATOR GROUP /////////////////////////////////////////
    Route::prefix('groups')->controller(CreatorGroupController::class)->group(function () {
        Route::post('/{group}/users/{user}', 'inviteUserToGroup');
        Route::post('/{group}', 'updateGroup');
        Route::delete('/{group}', 'deleteGroup');
    });

    /////////////////////////////// CREATOR EVENT /////////////////////////////////////////
    Route::prefix('events')->controller(CreatorEventController::class)->group(function () {
        Route::post('/{event}', 'updateEvent');
        Route::delete('/{event}', 'deleteEvent');
    });
});

// routes pour les créateurs, (delete update) éventuellement d'autres trucs


///////////////////////////////////////////////////////////////////////////////////
///////////////////////////////// ADMIN ROUTES ////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////
Route::prefix('admin')->group(function () {

    /////////////////////////////// ADMIN COMMENT /////////////////////////////////////////
    Route::controller(AdminCommentController::class)->group(function () {
        Route::get('events/{event}/comments', 'getEventComments');
        Route::get('comments/{comment}/replies', 'getCommentReplies');
        Route::post('events/{event}/comments', 'addEventComment');
        Route::post('comments/{comment}/replies', 'addCommentReply');
        Route::post('comments/{comment}/likes', 'addCommentLike');
        Route::delete('comments/{comment}/likes', 'deleteCommentLike');
        Route::post('comments/{comment}', 'updateComment');
        Route::delete('comments/{comment}', 'deleteComment');
    });

    ////////////////////////////////// ADMIN MESSAGE //////////////////////////////////////
    Route::controller(AdminMessageController::class)->group(function () {
        Route::get('groups/{group}/messages', 'getGroupMessages');
        Route::get('users/{user}/messages', 'getUserMessages');
        Route::delete('messages/{message}', 'deleteMessage');
    });

    /////////////////////////////// ADMIN EVENT /////////////////////////////////////////
    Route::prefix('events')->controller(AdminEventController::class)->group(function () {
        Route::get('/', 'getAllEvents');
        Route::get('/{event}', 'getEvent');
        Route::get('/{event}/users', 'getEventUsers');
        Route::get('/{event}/comments', 'getEventComments');
        Route::get('/{event}/images', 'getEventImages');
        Route::post('/{event}', 'updateEvent');
        Route::delete('/{event}', 'deleteEvent');
    });

    /////////////////////////////// ADMIN GROUP /////////////////////////////////////////
    Route::prefix('groups')->controller(AdminGroupController::class)->group(function () {
        Route::get('/', 'getAllGroups');
        Route::get('/{group}', 'getGroup');
        Route::get('/{group}/users', 'getGroupUsers');
        Route::get('/{group}/events', 'getGroupEvents');
        Route::get('/{group}/images', 'getGroupImages');
        Route::post('/{group}', 'updateGroup');
        Route::delete('/{group}', 'deleteGroup');
    });

    /////////////////////////////// ADMIN USER /////////////////////////////////////////
    Route::prefix('users')->controller(AdminUserController::class)->group(function () {
        Route::get('/', 'getAllUsers');
        Route::get('/{user}', 'getUser');
        Route::get('/{user}/comments', 'getUserComments');
        Route::get('/{user}/groups', 'getUserGroups');
        Route::get('/{user}/events', 'getUserEvents');
        Route::post('/', 'addUser');
        Route::post('/{user}', 'updateUser');
        Route::delete('/{user}', 'deleteUser');
    });
});


