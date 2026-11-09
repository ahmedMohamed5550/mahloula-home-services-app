<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeWorkController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SponserController;

Route::post('/register',[UserController::class,'register']);
Route::post('/login', [LoginController::class,'login']);
Route::post('refresh-token', [LoginController::class,'refreshToken']);
Route::get('/logout', [UserController::class,'logout'])->middleware(['auth:api']);

Route::post('/forgot-password', [UserController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('/reset-password', [UserController::class, 'resetPassword'])->name('password.update');

// Required for the Password::sendResetLink to work
Route::get('/reset-password/{token}', function ($token) {
    return response()->json(['token' => $token], 200);
})->name('password.reset');

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::controller(UserController::class)->group(function(){
    Route::Post('/user/editUserProfile/{id}','editUserProfile')->middleware(['auth:api','IsUser']);
    Route::get('/user/{user_id}','userProfile')->middleware(['auth:api','IsUser']);
});

Route::group(['prefix'=>'location'],function($router){
    Route::controller(LocationController::class)->group(function(){
        Route::delete('destroy/{id}', 'destroy')->middleware(['auth:api']);
        Route::post('store', 'store');
        Route::post('update/{id}', 'update')->middleware(['auth:api']);
        Route::get('showUsersLocation/{id}', 'showUsersLocation')->middleware(['auth:api']);
    });
});

Route::prefix('services')->group(function(){
    Route::post('/create', [ServiceController::class, 'store'])->middleware(['auth:api']);
    Route::get('/', [ServiceController::class, 'index']);
    Route::get('/show/{id}' , [ServiceController::class , 'show']);
    Route::get('/show/section/{section_id}' , [ServiceController::class , 'showAllServicesBySection']);
    Route::post('/edit/{id}' , [ServiceController::class , 'update'])->middleware(['auth:api']);
    Route::delete('/delete/{id}' , [ServiceController::class , 'delete'])->middleware(['auth:api']);
});

Route::prefix('like')->group(function(){
    Route::post('/create' , [LikeController::class , 'store'])->middleware(['auth:api','IsUser']);
    Route::get('/show/{id}' , [LikeController::class , 'showLikes'])->middleware(['auth:api','IsProvider']);
});

Route::prefix('sections')->group(function(){
    Route::post('/create', [SectionController::class, 'store'])->middleware(['auth:api']);
    Route::get('/', [SectionController::class, 'index']);
    Route::get('/show/{id}' , [SectionController::class , 'show']);
    Route::post('/edit/{id}' , [SectionController::class , 'update'])->middleware(['auth:api']);
    Route::delete('/delete/{id}' , [SectionController::class , 'delete'])->middleware(['auth:api']);
});

Route::controller(EmployeeController::class)->prefix('employee')->group(function(){
    Route::post('/employeeCompleteData','employeeCompleteData');
    Route::post('/changeCheckByAdmin/{id}','changeCheckByAdmin');
    Route::post('/editEmployeeCompleteData/{id}','updateEmployeeCompleteData')->middleware(['auth:api','IsProvider']);
    Route::get('/section/{section_id}/service/{service_id}/{user_type}','showAllEmployeesBySectionIdAndServiceId');
    Route::get("/employeeProfile/{user_id}",'employeeProfile')->middleware(['auth:api']);
    Route::post("/showAllEmployeeBylocation/{city}",'showAllEmployeeBylocation')->middleware(['auth:api','IsUser']);
    Route::post('/changeEmployeeStatus/{id}', 'changeEmployeeStatus')->middleware(['auth:api']);
    Route::post('/changeCheckByAdmin/{id}', 'changeCheckByAdmin')->middleware(['auth:api']);
});

Route::controller(FeedbackController::class)->prefix('feedback')->group(function () {
    Route::post('/create','store')->middleware(['auth:api','IsUser']);
    Route::get('/getEmployeeFeedback/{id}', 'getEmployeeFeedback')->middleware(['auth:api','IsProvider']);
    Route::delete('/delete/{id}', 'delete')->middleware(['auth:api','IsUser']);
    Route::post('/edit/{id}', 'update')->middleware(['auth:api','IsUser']);
});

Route::controller(AdminController::class)->prefix('Admin')->group(function(){
    Route::post('/changeEmployeeStatus/{id}', 'changeEmployeeStatus')->middleware(['auth:api']);
    Route::post('/changeCheckByAdmin/{id}', 'changeCheckByAdmin')->middleware(['auth:api']);
});

Route::controller(EmployeeWorkController::class)->prefix('work')->group(function(){
    Route::post('/create', 'store')->middleware(['auth:api','IsProvider']);
    Route::post('/edit', 'update')->middleware(['auth:api','IsProvider']);
});

Route::group(['prefix'=>'projects'],function($router){
    Route::controller(ProjectController::class)->group(function(){
        Route::get('/index', 'index')->middleware(['auth:api']);
        Route::post('/create', 'store')->middleware(['auth:api']);
        Route::get('/show/{id}', 'show')->middleware(['auth:api']);
        Route::get('/check-project-author/{projectId}', 'checkProjectAuthor')->middleware(['auth:api']);
        Route::get('/check-project-comment-author/{projectId}/{commentId}', 'checkProjectCommentAuthor')->middleware(['auth:api']);
        Route::post('/update/{id}', 'update')->middleware(['auth:api']);
        Route::post('/{projectId}/like/{userId}', 'like')->middleware(['auth:api']);
        Route::get('/total-likes/{projectId}', 'getTotalLikes')->middleware(['auth:api']);
        Route::delete('/destroy/{id}', 'destroy')->middleware(['auth:api']);
        Route::post('/{project_id}/comment/{user_id}', 'createComment')->middleware(['auth:api']);
        Route::get('/total-comments/{projectId}', 'showComment')->middleware(['auth:api']);
        Route::post('/update/{project_id}/comment/{comment_id}', 'updateComment')->middleware(['auth:api']);
        Route::delete('/delete/{project_id}/comment/{comment_id}', 'deleteComment')->middleware(['auth:api']);
    });
});

Route::prefix('save')->group(function(){
    Route::post('/create' , [FavouriteController::class , 'store'])->middleware(['auth:api','IsUser']);
    Route::get('/show/{id}' , [FavouriteController::class , 'show'])->middleware(['auth:api','IsUser']);
});

Route::prefix('contact')->group(function(){
    Route::get('/show/{id}' , [ContactController::class , 'show']);
});

Route::prefix('sponsers')->group(function(){
    Route::get('/index' , [SponserController::class , 'index']);
});







