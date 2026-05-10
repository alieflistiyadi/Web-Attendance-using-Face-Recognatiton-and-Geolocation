<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiswaController;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\JurusanController;

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

    //Attendance
    route::get('/attendance/create', [App\Http\Controllers\AttendanceController::class, 'create'])->name('attendance.create');
    route::post('/attendance/store', [App\Http\Controllers\AttendanceController::class, 'store'])->name('attendance.store');

    // Edit Profile
    route::get('/editprofile', [App\Http\Controllers\AttendanceController::class, 'editprofile'])->name('editprofile');
    route::post('/attendance/{nis}/updateprofile', [App\Http\Controllers\AttendanceController::class, 'updateprofile'])->name('updateprofile');

    // Histori
    route::get('/attendance/histori', [App\Http\Controllers\AttendanceController::class, 'histori'])->name('histori');
    route::post('/gethistori', [App\Http\Controllers\AttendanceController::class, 'gethistori'])->name('gethistori');

    // Izin
    route::get('attendance/izin', [App\Http\Controllers\AttendanceController::class, 'izin'])->name('izin');
    route::get('attendance/buatizin', [App\Http\Controllers\AttendanceController::class, 'buatizin'])->name('buatizin');
    route::post('attendance/storeizin', [App\Http\Controllers\AttendanceController::class, 'storeizin'])->name('storeizin');
});

Route::middleware(['auth:user'])->group(function () {
    route::get('/proseslogoutadmin', [App\Http\Controllers\AuthController::class, 'proseslogoutadmin']);
    Route::get('/panel/dashboardadmin', [DashboardController::class, 'dashboardadmin']);
    
    //Siswa
    route::get('/siswa', [SiswaController::class, 'index']);
    route::post('/siswa/store', [SiswaController::class, 'store']);
    route::post('/siswa/edit', [SiswaController::class, 'edit']);
    route::post('/siswa/{nis}/update', [SiswaController::class, 'update']);
    route::post('/siswa/{nis}/delete', [SiswaController::class, 'delete']);

    //jurusan
    Route::get('/jurusan', [JurusanController::class, 'index']);
    Route::post('/jurusan/store', [JurusanController::class, 'store']);
    Route::post('/jurusan/edit', [JurusanController::class, 'edit']);
    Route::post('/jurusan/{kode_jurusan}/update', [JurusanController::class, 'update']);
    Route::post('/jurusan/{kode_jurusan}/delete', [JurusanController::class, 'delete']);

    //Attendance
    Route::get('/attendance/monitoring', [App\Http\Controllers\AttendanceController::class, 'monitoring'])->name('monitoring');
    Route::post('/getattendance', [App\Http\Controllers\AttendanceController::class, 'getattendance'])->name('getattendance');
});