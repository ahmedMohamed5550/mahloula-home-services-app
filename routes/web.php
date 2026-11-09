<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\SponserController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\ProjectController;
use App\Http\Controllers\admin\SectionController;
use App\Http\Controllers\admin\ServiceController;
use App\Http\Controllers\admin\EmployeeController;


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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/' , function(){
    return view('admin.login');
});

Route::post('/admin/login' , [AdminController::class , 'login'])->name('login');

Route::get('/admin/logout', [AdminController::class,'logout'])->name('logout');

Route::get('admin/dashboard' , [AdminController::class , 'home'])->name('admin.dashboard');

Route::get('admin/create' , [AdminController::class , 'create'])->name('admin.create');
Route::post('admin/store' , [AdminController::class , 'store'])->name('admin.store');

Route::get('admin/waitingEmployee' , [EmployeeController::class , 'waitingEmployee'])->name('admin.employee.waiting');
Route::get('admin/waitingEmployee/{id}' , [EmployeeController::class , 'OneWaitingEmployee'])->name('admin.employee.waitingOne');

Route::get('admin/acceptEmployee' , [EmployeeController::class , 'acceptEmployee'])->name('admin.employee.accept');
Route::get('admin/acceptEmployee/{id}' , [EmployeeController::class , 'OneAcceptEmployee'])->name('admin.employee.acceptOne');

Route::get('admin/updateEmployee' , [EmployeeController::class , 'updateEmployee'])->name('admin.employee.update');
Route::get('admin/updateEmployee/{id}' , [EmployeeController::class , 'OneUpdateEmployee'])->name('admin.employee.updateOne');

Route::get('admin/accept/employee/{id}' , [EmployeeController::class , 'accept'])->name('admin.acceptEmployee');
Route::get('admin/update/employee/{id}' , [EmployeeController::class , 'update'])->name('admin.updateEmployee');
Route::get('admin/reject/employee/{id}' , [EmployeeController::class , 'reject'])->name('admin.rejectEmployee');


Route::get('admin/project/all' , [ProjectController::class , 'show'])->name('admin.project.show');
Route::get('admin/prject/{id}' , [ProjectController::class , 'showOne'])->name('admin.project.showOne');
Route::delete('admin/prject/{id}' , [ProjectController::class , 'delete'])->name('admin.project.delete');
Route::delete('admin/prject/comment/{id}' , [ProjectController::class , 'deleteComment'])->name('admin.project.comment.delete');


Route::get('section/All' , [SectionController::class , 'show'])->name('section.show');
Route::get('section/create' , [SectionController::class , 'create'])->name('section.create');
Route::post('section/store' , [SectionController::class , 'store'])->name('section.store');
Route::get('section/update/{id}' , [SectionController::class , 'edit'])->name('section.edit');
Route::post('section/update/{id}' , [SectionController::class , 'update'])->name('section.update');
Route::delete('section/delete/{id}' , [SectionController::class , 'delete'])->name('section.delete');





Route::get('service/All' , [ServiceController::class , 'show'])->name('service.show');
Route::get('service/create' , [ServiceController::class , 'create'])->name('service.create');
Route::post('service/store' , [ServiceController::class , 'store'])->name('service.store');
Route::get('service/update/{id}' , [ServiceController::class , 'edit'])->name('service.edit');
Route::post('service/update/{id}' , [ServiceController::class , 'update'])->name('service.update');
Route::delete('service/delete/{id}' , [ServiceController::class , 'delete'])->name('service.delete');


Route::get('sponser/All' , [SponserController::class , 'show'])->name('sponser.show');
Route::get('sponser/{id}' , [SponserController::class , 'showOne'])->name('sponser.showOne');
Route::get('admin/sponser/create' , [SponserController::class , 'create'])->name('sponser.create');
Route::post('sponser/store' , [SponserController::class , 'store'])->name('sponser.store');
Route::get('sponser/update/{id}' , [SponserController::class , 'edit'])->name('sponser.edit');
Route::post('sponser/update/{id}' , [SponserController::class , 'update'])->name('sponser.update');
Route::delete('sponser/delete/{id}' , [SponserController::class , 'delete'])->name('sponser.delete');

Route::get('admin/user/show' , [AdminController::Class , 'showUser'])->name('admin.user.show');
Route::get('admin/user/show/{id}' , [AdminController::Class , 'showOne'])->name('admin.user.showOne');
Route::delete('admin/user/delete/{id}' , [AdminController::Class , 'deletUser'])->name('admin.user.delete');


