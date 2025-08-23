<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

///////////////////////////////////////////////////////////////////////////////////
///////////////////////////////// USER ROUTES /////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////

/////////////////////////////// EVENT /////////////////////////////////////////
Route::prefix('events')->controller(EventController::class)->group(function () {
    Route::get('/{id}', 'getEvent');
    Route::get('/{id}/users', 'getEventUsers');
    Route::get('/{id}/comments', 'getEventComments');
    Route::get('/{id}/images', 'getEventImages');
    Route::post('/', 'addEvent');
    Route::post('/{id}/participate', 'joinEvent');
    Route::delete('/{id}/participate', 'leaveEvent');
});

/////////////////////////////// GROUP /////////////////////////////////////////
Route::prefix('groups')->controller(GroupController::class)->group(function () {
    Route::get('/{id}', 'getGroup');
    Route::get('/{id}/events', 'getGroupEvents');
    Route::get('/{id}/users', 'getGroupUsers');
    Route::get('/{id}/messages', 'getGroupMessages');
    Route::get('/{id}/images', 'getGroupImages');
    Route::post('/', 'addGroup');
    Route::post('/{id}/status', 'updateGroupUserStatus');
});

////////////////////////////////// USER ////////////////////////////////////////////////
Route::prefix('user')->controller(UserController::class)->group(function () {
    Route::get('/', 'getUser');
    Route::get('/groups', 'getUserGroups'); // eventually bind incoming events for the user groups interface
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

// routes pour les créateurs, (delete update) éventuellement d'autres trucs


///////////////////////////////////////////////////////////////////////////////////
///////////////////////////////// ADMIN ROUTES ////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////
Route::prefix('admin')->group(function () {

    /////////////////////////////// ADMIN COMMENT /////////////////////////////////////////
    Route::prefix('comments')->controller(AdminCommentController::class)->group(function () {
        Route::get('/', 'getAllComments');
        Route::get('/{id}', 'getComment');
        Route::get('/{id}/replies', 'getCommentReplies');
        Route::post('/', 'addComment');
        Route::post('/{id}', 'updateComment');
        Route::delete('/{id}', 'deleteComment');
    });

    /////////////////////////////// ADMIN MESSAGE /////////////////////////////////////////
    Route::prefix('messages')->controller(AdminMessageController::class)->group(function () {
        Route::get('/', 'getAllMessages');
        Route::get('/{id}', 'getMessage');
        Route::post('/', 'addMessage');
        Route::post('/{id}', 'updateMessage');
        Route::delete('/{id}', 'deleteMessage');
    });

    /////////////////////////////// ADMIN EVENT /////////////////////////////////////////
    Route::prefix('events')->controller(AdminEventController::class)->group(function () {
        Route::get('/', 'getAllEvents');
        Route::get('/{id}', 'getEvent');
        Route::get('/{id}/users', 'getEventUsers');
        Route::get('/{id}/comments', 'getEventComments');
        Route::get('/{id}/images', 'getEventImages');
        Route::post('/', 'addEvent');
        Route::post('/{id}', 'updateEvent');
        Route::delete('/{id}', 'deleteEvent');
    });

    /////////////////////////////// ADMIN GROUP /////////////////////////////////////////
    Route::prefix('groups')->controller(AdminGroupController::class)->group(function () {
        Route::get('/', 'getAllGroups');
        Route::get('/{id}', 'getGroup');
        Route::get('/{id}/users', 'getGroupUsers');
        Route::get('/{id}/events', 'getGroupEvents');
        Route::get('/{id}/messages', 'getGroupMessages');
        Route::get('/{id}/images', 'getGroupImages');
        Route::post('/', 'addGroup');
        Route::post('/{id}', 'updateGroup');
        Route::delete('/{id}', 'deleteGroup');
    });

    /////////////////////////////// ADMIN USER /////////////////////////////////////////
    Route::prefix('users')->controller(AdminUserController::class)->group(function () {
        Route::get('/', 'getAllUsers');
        Route::get('/{id}', 'getUser');
        Route::get('/{id}/comments', 'getUserComments');
        Route::get('/{id}/groups', 'getUserGroups');
        Route::get('/{id}/messages', 'getUserMessages');
        Route::get('/{id}/events', 'getUserEvents');
        Route::post('/', 'addUser');
        Route::post('/{id}', 'updateUser');
        Route::delete('/{id}', 'deleteUser');
    });

});


