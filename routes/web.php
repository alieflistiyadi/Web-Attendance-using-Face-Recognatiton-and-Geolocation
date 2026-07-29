<?php

use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiswaController;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\ImportController;

Route::get('/', function () {
    return view('auth.login');
})->name('login');



route::middleware('guest:siswa')->group(function () {
    route::get('/', function () {
        return view('auth.login');
    })->name('login');
    route::post('/process-login', [App\Http\Controllers\AuthController::class, 'proseslogin'])->name('process-login');

    // Forgot Password - Siswa
    route::get('/forgot-password', [App\Http\Controllers\AuthController::class, 'showForgotPassword'])->name('forgot-password');
    route::post('/forgot-password', [App\Http\Controllers\AuthController::class, 'submitForgotPassword'])->name('forgot-password.submit');
});



// ===================== KELOLA RESET PASSWORD - ADMIN =====================
route::middleware('auth:user')->group(function () {
    route::get('/panel/reset-password-siswa', [App\Http\Controllers\AuthController::class, 'listResetRequests'])->name('admin.resetpassword.list');
    route::post('/panel/reset-password-siswa/{id}/approve', [App\Http\Controllers\AuthController::class, 'approveResetRequest'])->name('admin.resetpassword.approve');
    route::post('/panel/reset-password-siswa/{id}/reject', [App\Http\Controllers\AuthController::class, 'rejectResetRequest'])->name('admin.resetpassword.reject');
});

route::middleware('guest:user')->group(function () {
    route::get('/panel', function () {
        return view('auth.loginadmin');
    })->name('loginadmin');
});

Route::middleware('auth:siswa')->group(function () {
    Route::get('/ubah-password', function () {
        return view('auth.ubahpassword');
    });
});

route::post('/prosesloginadmin', [App\Http\Controllers\AuthController::class, 'prosesloginadmin']);

// ===================== SISWA ROUTES =====================
route::middleware('auth:siswa')->group(function () {

    route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard');

    route::get('/process-logout', [App\Http\Controllers\AuthController::class, 'proseslogout'])
        ->name('process-logout');

    // ✅ Chatbot untuk SISWA
    Route::post('/chatbot', [ChatbotController::class, 'chat']);

    // Attendance
    route::get('/attendance/create', [App\Http\Controllers\AttendanceController::class, 'create'])
        ->name('attendance.create');
    route::post('/attendance/store', [App\Http\Controllers\AttendanceController::class, 'store'])
        ->name('attendance.store');

    // Edit Profile
    route::get('/editprofile', [App\Http\Controllers\AttendanceController::class, 'editprofile'])
        ->name('editprofile');
    route::post('/attendance/{nis}/updateprofile', [App\Http\Controllers\AttendanceController::class, 'updateprofile'])
        ->name('updateprofile');
    route::post('/siswa/save-descriptor', [App\Http\Controllers\AttendanceController::class, 'saveDescriptor'])
        ->name('siswa.saveDescriptor');
    route::get('/siswa/face-descriptors', [App\Http\Controllers\AttendanceController::class, 'getFaceDescriptors'])
        ->name('siswa.faceDescriptors');

    // Histori
    route::get('/attendance/histori', [App\Http\Controllers\AttendanceController::class, 'histori'])
        ->name('histori');
    route::post('/gethistori', [App\Http\Controllers\AttendanceController::class, 'gethistori'])
        ->name('gethistori');

    // Izin
    route::get('/attendance/izin', [App\Http\Controllers\AttendanceController::class, 'izin'])
        ->name('izin');
    route::get('/attendance/buatizin', [App\Http\Controllers\AttendanceController::class, 'buatizin'])
        ->name('buatizin');
    route::post('/attendance/storeizin', [App\Http\Controllers\AttendanceController::class, 'storeizin'])
        ->name('storeizin');
    Route::post('/attendance/cekpengajuanizin', [App\Http\Controllers\AttendanceController::class, 'cekpengajuanizin'])
        ->name('attendance.cekpengajuanizin');
    Route::get('/attendance/editizin/{id}', [App\Http\Controllers\AttendanceController::class, 'editizin'])
        ->name('attendance.editizin');
    Route::post('/attendance/updateizin/{id}', [App\Http\Controllers\AttendanceController::class, 'updateizin'])
        ->name('attendance.updateizin');
    Route::get('/attendance/deleteizin/{id}', [App\Http\Controllers\AttendanceController::class, 'deleteizin'])
        ->name('attendance.deleteizin');
});

// ===================== ADMIN ROUTES =====================
Route::middleware(['auth:user'])->group(function () {
    route::get('/proseslogoutadmin', [App\Http\Controllers\AuthController::class, 'proseslogoutadmin']);
    Route::get('/panel/dashboardadmin', [DashboardController::class, 'dashboardadmin'])->name('dashboardadmin');
    Route::get('/panel/jurusan/{kode}', [DashboardController::class, 'kelas']);
    Route::get('/panel/rekap/{kode}/{kelas}/{bulan}/{tahun}', [DashboardController::class, 'rekapBulanan']);

    // ✅ Chatbot untuk ADMIN (route berbeda agar tidak bentrok)
    Route::post('/panel/chatbot', [ChatbotController::class, 'chat']);

    // Siswa
    route::get('/siswa/kelas/{kelas}', [SiswaController::class, 'indexKelas'])->name('siswa.kelas');
    route::get('/siswa', [SiswaController::class, 'index']);
    route::post('/siswa/store', [SiswaController::class, 'store']);
    route::post('/siswa/edit', [SiswaController::class, 'edit']);
    route::post('/siswa/{nis}/update', [SiswaController::class, 'update']);
    route::post('/siswa/{nis}/delete', [SiswaController::class, 'delete']);
    Route::post('/siswa/import', [ImportController::class, 'import'])->name('siswa.import');
    Route::get('/siswa/template', [ImportController::class, 'downloadTemplate'])
        ->name('siswa.template');

    // Guru
    Route::get('/guru', [GuruController::class, 'index']);
    Route::post('/guru/store', [GuruController::class, 'store']);
    Route::post('/guru/edit', [GuruController::class, 'edit']);
    Route::post('/guru/{id}/update', [GuruController::class, 'update']);
    Route::post('/guru/{id}/delete', [GuruController::class, 'delete']);

    // Jurusan
    Route::get('/jurusan', [JurusanController::class, 'index']);
    Route::post('/jurusan/store', [JurusanController::class, 'store']);
    Route::post('/jurusan/edit', [JurusanController::class, 'edit']);
    Route::post('/jurusan/{kode_jurusan}/update', [JurusanController::class, 'update']);
    Route::post('/jurusan/{kode_jurusan}/delete', [JurusanController::class, 'delete']);

    // Attendance Admin
    Route::get('/attendance/monitoring', [App\Http\Controllers\AttendanceController::class, 'monitoring'])->name('monitoring');
    Route::get('/attendance/monitoring/kelas/{kelas}', [App\Http\Controllers\AttendanceController::class, 'monitoringKelas'])->name('monitoring.kelas');
    Route::post('/getattendance', [App\Http\Controllers\AttendanceController::class, 'getattendance'])->name('getattendance');
    Route::post('/getattendancekelas', [App\Http\Controllers\AttendanceController::class, 'getattendancekelas'])->name('getattendancekelas');
    Route::post('/tampilkanpeta', [App\Http\Controllers\AttendanceController::class, 'tampilkanpeta'])->name('tampilkanpeta');
    Route::get('/attendance/laporan', [App\Http\Controllers\AttendanceController::class, 'halamanlaporan'])->name('attendance.laporan');
    Route::post('/attendance/cetaklaporan', [App\Http\Controllers\AttendanceController::class, 'cetaklaporan'])->name('attendance.cetaklaporan');
    Route::get('/attendance/rekap', [App\Http\Controllers\AttendanceController::class, 'rekap'])->name('attendance.rekap');
    Route::post('/attendance/cetakrekap', [App\Http\Controllers\AttendanceController::class, 'cetakrekap'])->name('attendance.cetakrekap');

    // Izin Sakit
    Route::get('/attendance/izinsakit/kelas/{kelas}', [App\Http\Controllers\AttendanceController::class, 'listIzinSakitKelas'])->name('attendance.izinsakit.kelas');
    Route::get('/attendance/izinsakit', [App\Http\Controllers\AttendanceController::class, 'jurusanIzin'])->name('attendance.izinsakit');
    Route::post('/attendance/approveizinsakit', [App\Http\Controllers\AttendanceController::class, 'approveizinsakit'])->name('attendance.approveizinsakit');
    Route::get('/attendance/{id}/batalkanizinsakit', [App\Http\Controllers\AttendanceController::class, 'batalkanizinsakit'])->name('attendance.batalkanizinsakit');
    Route::post('/attendance/updatePassword', [App\Http\Controllers\AttendanceController::class, 'updatePassword'])->name('attendance.updatePassword');
    Route::post('/attendance/cekpengajuanizin', [App\Http\Controllers\AttendanceController::class, 'cekpengajuanizin'])->name('attendance.cekpengajuanizin');

    // Konfigurasi
    Route::get('/konfigurasi/lokasisekolah', [App\Http\Controllers\KonfigurasiController::class, 'lokasisekolah'])->name('konfigurasi.lokasisekolah');
    Route::post('/konfigurasi/updatelokasisekolah', [App\Http\Controllers\KonfigurasiController::class, 'updatelokasisekolah'])->name('konfigurasi.updatelokasisekolah');
    Route::get('/konfigurasi/waktu', [App\Http\Controllers\KonfigurasiController::class, 'konfigurasiWaktu'])->name('konfigurasi.waktu');
    Route::post('/konfigurasi/updatewaktu', [App\Http\Controllers\KonfigurasiController::class, 'updateWaktu'])->name('konfigurasi.updatewaktu');
    // Setting Admin
    Route::get('/panel/setting', [App\Http\Controllers\AdminController::class, 'setting']);
    Route::post('/panel/setting/update', [App\Http\Controllers\AdminController::class, 'updateSetting']);
});

// ini kode web.php