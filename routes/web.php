<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

/*a
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
})->name('login');


route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
route::post('/process-login', [App\Http\Controllers\AuthController::class, 'proseslogin'])->name('process-login');


route::middleware('guest:siswa')->group(function () {
    route::get('/', function () {
        return view('auth.login');
    })->name('login');
    route::post('/process-login', [App\Http\Controllers\AuthController::class, 'proseslogin'])->name('process-login');
});

route::middleware('guest:user')->group(function () {
    route::get('/panel', function () {
        return view('auth.loginadmin');
    })->name('loginadmin');
    });

    route::post('/prosesloginadmin', [App\Http\Controllers\AuthController::class, 'prosesloginadmin']);


route::middleware('auth:siswa')->group(function () {
    route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    route::get('/process-logout', [App\Http\Controllers\AuthController::class, 'proseslogout'])->name('process-logout');

    route::get('/attendance/create', [App\Http\Controllers\AttendanceController::class, 'create'])->name('attendance.create');
    route::post('/attendance/store', [App\Http\Controllers\AttendanceController::class, 'store'])->name('attendance.store');

    route::get('/editprofile', [App\Http\Controllers\AttendanceController::class, 'editprofile'])->name('editprofile');
    route::post('/attendance/{$nis}/updateprofile', [App\Http\Controllers\AttendanceController::class, 'updateprofile'])->name('updateprofile');
});

Route::middleware(['auth:user'])->group(function (){
    route::get('/proseslogoutadmin', [App\Http\Controllers\AuthController::class, 'proseslogoutadmin']);
    Route::get('/panel/dashboardadmin',[DashboardController::class,'dashboardadmin']);
});
