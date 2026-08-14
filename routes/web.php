<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetRequestController;
use App\Http\Controllers\ForcedPasswordController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\KonfigurasiController;
use App\Http\Controllers\AdminController;


/*
|--------------------------------------------------------------------------
| LOGIN SISWA
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth.login');
})->name('login');


Route::middleware('guest:siswa')->group(function () {

    Route::get('/', function () {
        return view('auth.login');
    })->name('login');

    Route::post(
        '/process-login',
        [AuthController::class, 'proseslogin']
    )->name('process-login');


    /*
    |--------------------------------------------------------------------------
    | Forgot Password - Siswa
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/forgot-password',
        [AuthController::class, 'showForgotPassword']
    )->name('forgot-password');

    Route::post(
        '/forgot-password',
        [AuthController::class, 'submitForgotPassword']
    )->name('forgot-password.submit');


    // Cek NIS sebelum mengirim OTP
    Route::post(
        '/forgot-password/check',
        [AuthController::class, 'checkForgotPassword']
    )->name('forgot.password.check');


    // Kirim OTP
    Route::post(
        '/forgot-password/send-otp',
        [AuthController::class, 'sendOtp']
    )->name('forgot.password.sendotp');


    // Halaman verifikasi OTP
    Route::get(
        '/verify-otp',
        [AuthController::class, 'showVerifyOtp']
    )->name('verify.otp');

    Route::post(
        '/verify-otp',
        [AuthController::class, 'verifyOtp']
    )->name('verify.otp.submit');


    // Kirim ulang OTP melalui WhatsApp
    Route::post(
        '/resend-otp',
        [AuthController::class, 'resendOtp']
    )->name('otp.resend');


    // Reset password
    Route::get(
        '/reset-password',
        [AuthController::class, 'showResetPassword']
    )->name('reset.password');

    Route::post(
        '/reset-password',
        [AuthController::class, 'updatePassword']
    )->name('reset.password.update');
});


/*
|--------------------------------------------------------------------------
| LOGIN ADMIN / GURU
|--------------------------------------------------------------------------
*/

Route::middleware('guest:user')->group(function () {

    Route::get('/panel', function () {
        return view('auth.loginadmin');
    })->name('loginadmin');


    /*
    |--------------------------------------------------------------------------
    | Forgot Password - Admin/Guru
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/forgot-password-admin',
        [PasswordResetRequestController::class, 'showForm']
    )->name('forgot-password-admin');

    Route::post(
        '/forgot-password-admin',
        [PasswordResetRequestController::class, 'submit']
    )->name('forgot-password-admin.submit');
});


/*
|--------------------------------------------------------------------------
| UBAH PASSWORD SISWA
|--------------------------------------------------------------------------
*/

Route::middleware('auth:siswa')->group(function () {

    Route::get('/ubah-password', function () {
        return view('auth.ubahpassword');
    });
});


/*
|--------------------------------------------------------------------------
| PROSES LOGIN ADMIN
|--------------------------------------------------------------------------
*/

Route::post(
    '/prosesloginadmin',
    [AuthController::class, 'prosesloginadmin']
);


/*
|--------------------------------------------------------------------------
| PASSWORD WAJIB DIGANTI
|--------------------------------------------------------------------------
*/

Route::middleware('auth:user')->group(function () {

    Route::get(
        '/ubah-password-wajib',
        [ForcedPasswordController::class, 'edit']
    )->name('ubah-password-wajib');

    Route::post(
        '/ubah-password-wajib',
        [ForcedPasswordController::class, 'update']
    )->name('ubah-password-wajib.update');
});


/*
|--------------------------------------------------------------------------
| SISWA ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth:siswa')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard Siswa
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Logout Siswa
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/process-logout',
        [AuthController::class, 'proseslogout']
    )->name('process-logout');


    /*
    |--------------------------------------------------------------------------
    | Chatbot Siswa
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/chatbot',
        [ChatbotController::class, 'chatSiswa']
    );


    /*
    |--------------------------------------------------------------------------
    | Attendance Siswa
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/attendance/create',
        [AttendanceController::class, 'create']
    )->name('attendance.create');

    Route::post(
        '/attendance/store',
        [AttendanceController::class, 'store']
    )->name('attendance.store');


    /*
    |--------------------------------------------------------------------------
    | Edit Profile Siswa
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/editprofile',
        [AttendanceController::class, 'editprofile']
    )->name('editprofile');

    Route::post(
        '/attendance/{nis}/updateprofile',
        [AttendanceController::class, 'updateprofile']
    )->name('updateprofile');


    /*
    |--------------------------------------------------------------------------
    | Face Descriptor
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/siswa/save-descriptor',
        [AttendanceController::class, 'saveDescriptor']
    )->name('siswa.saveDescriptor');

    Route::get(
        '/siswa/face-descriptors',
        [AttendanceController::class, 'getFaceDescriptors']
    )->name('siswa.faceDescriptors');


    /*
    |--------------------------------------------------------------------------
    | Histori Attendance Siswa
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/attendance/histori',
        [AttendanceController::class, 'histori']
    )->name('histori');

    Route::post(
        '/gethistori',
        [AttendanceController::class, 'gethistori']
    )->name('gethistori');


    /*
    |--------------------------------------------------------------------------
    | Izin Siswa
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/attendance/izin',
        [AttendanceController::class, 'izin']
    )->name('izin');

    Route::get(
        '/attendance/buatizin',
        [AttendanceController::class, 'buatizin']
    )->name('buatizin');

    Route::post(
        '/attendance/storeizin',
        [AttendanceController::class, 'storeizin']
    )->name('storeizin');

    Route::post(
        '/attendance/cekpengajuanizin',
        [AttendanceController::class, 'cekpengajuanizin']
    )->name('attendance.cekpengajuanizin');

    Route::get(
        '/attendance/editizin/{id}',
        [AttendanceController::class, 'editizin']
    )->name('attendance.editizin');

    Route::post(
        '/attendance/updateizin/{id}',
        [AttendanceController::class, 'updateizin']
    )->name('attendance.updateizin');

    Route::get(
        '/attendance/deleteizin/{id}',
        [AttendanceController::class, 'deleteizin']
    )->name('attendance.deleteizin');
});


/*
|--------------------------------------------------------------------------
| ADMIN / GURU ROUTES
|--------------------------------------------------------------------------
|
| Semua user guru dan superadmin harus login menggunakan guard user
| dan sudah melewati force.change.password.
|
*/

Route::middleware([
    'auth:user',
    'force.change.password'
])->group(function () {


    /*
    |--------------------------------------------------------------------------
    | Logout Admin / Guru
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/proseslogoutadmin',
        [AuthController::class, 'proseslogoutadmin']
    );


    /*
    |--------------------------------------------------------------------------
    | Dashboard Admin / Guru
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/panel/dashboardadmin',
        [DashboardController::class, 'dashboardadmin']
    )->name('dashboardadmin');


    Route::get(
        '/panel/jurusan/{kode}',
        [DashboardController::class, 'kelas']
    );


    Route::get(
        '/panel/rekap/{kode}/{kelas}/{bulan}/{tahun}',
        [DashboardController::class, 'rekapBulanan']
    );


    /*
    |--------------------------------------------------------------------------
    | Chatbot Admin / Guru
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/panel/chatbot',
        [ChatbotController::class, 'chatAdmin']
    );


    /*
    |--------------------------------------------------------------------------
    | DATA SISWA
    |--------------------------------------------------------------------------
    |
    | Untuk sementara tetap berada di auth:user seperti kode sebelumnya.
    | Pembatasan lebih detail dapat dilakukan setelah struktur kelas
    | dan mata pelajaran selesai.
    |
    */

    Route::get(
        '/siswa/kelas/{kelas}',
        [SiswaController::class, 'indexKelas']
    )->name('siswa.kelas');

    Route::get(
        '/siswa',
        [SiswaController::class, 'index']
    );

    Route::post(
        '/siswa/store',
        [SiswaController::class, 'store']
    );

    Route::post(
        '/siswa/edit',
        [SiswaController::class, 'edit']
    );

    Route::post(
        '/siswa/{nis}/update',
        [SiswaController::class, 'update']
    );

    Route::post(
        '/siswa/{nis}/delete',
        [SiswaController::class, 'delete']
    );

    Route::post(
        '/siswa/import',
        [ImportController::class, 'import']
    )->name('siswa.import');

    Route::get(
        '/siswa/template',
        [ImportController::class, 'downloadTemplate']
    )->name('siswa.template');


    /*
    |--------------------------------------------------------------------------
    | GURU MANAGEMENT - KHUSUS SUPERADMIN
    |--------------------------------------------------------------------------
    */
    // ================================
// JADWAL PELAJARAN
// ================================

    Route::get(
        '/jadwal-pelajaran',
        [App\Http\Controllers\JadwalPelajaranController::class, 'index']
    )->name('jadwal.index');


    // Penugasan Guru + Mapel + Kelas
    Route::post(
        '/jadwal-pelajaran/penugasan',
        [App\Http\Controllers\JadwalPelajaranController::class, 'storePenugasan']
    )->name('jadwal.penugasan.store');

    Route::put(
        '/jadwal-pelajaran/penugasan/{id}',
        [App\Http\Controllers\JadwalPelajaranController::class, 'updatePenugasan']
    )->name('jadwal.penugasan.update');

    Route::delete(
        '/jadwal-pelajaran/penugasan/{id}',
        [App\Http\Controllers\JadwalPelajaranController::class, 'deletePenugasan']
    )->name('jadwal.penugasan.delete');


    // Jadwal
    Route::post(
        '/jadwal-pelajaran',
        [App\Http\Controllers\JadwalPelajaranController::class, 'storeJadwal']
    )->name('jadwal.store');

    Route::put(
        '/jadwal-pelajaran/{id}',
        [App\Http\Controllers\JadwalPelajaranController::class, 'updateJadwal']
    )->name('jadwal.update');

    Route::delete(
        '/jadwal-pelajaran/{id}',
        [App\Http\Controllers\JadwalPelajaranController::class, 'deleteJadwal']
    )->name('jadwal.delete');
    Route::middleware('role:superadmin')->group(function () {

        Route::get(
            '/guru',
            [GuruController::class, 'index']
        );

        Route::post(
            '/guru/store',
            [GuruController::class, 'store']
        );

        Route::post(
            '/guru/edit',
            [GuruController::class, 'edit']
        );

        Route::post(
            '/guru/{id}/update',
            [GuruController::class, 'update']
        );

        Route::post(
            '/guru/{id}/delete',
            [GuruController::class, 'delete']
        );


        /*
        |--------------------------------------------------------------------------
        | PERMINTAAN RESET PASSWORD - KHUSUS SUPERADMIN
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/panel/reset-requests',
            [PasswordResetRequestController::class, 'index']
        )->name('reset-requests.index');

        Route::post(
            '/panel/reset-requests/{id}/approve',
            [PasswordResetRequestController::class, 'approve']
        )->name('reset-requests.approve');
    });


    /*
    |--------------------------------------------------------------------------
    | JURUSAN
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/jurusan',
        [JurusanController::class, 'index']
    );

    Route::post(
        '/jurusan/store',
        [JurusanController::class, 'store']
    );

    Route::post(
        '/jurusan/edit',
        [JurusanController::class, 'edit']
    );

    Route::post(
        '/jurusan/{kode_jurusan}/update',
        [JurusanController::class, 'update']
    );

    Route::post(
        '/jurusan/{kode_jurusan}/delete',
        [JurusanController::class, 'delete']
    );


    /*
    |--------------------------------------------------------------------------
    | ================================================================
    | ATTENDANCE MONITORING - KHUSUS GURU
    | ================================================================
    |--------------------------------------------------------------------------
    |
    | POINT 4 REVISI:
    |
    | "The one who checked the attendance is the teacher not the operator"
    |
    | Dengan middleware role:guru:
    |
    | Guru       -> BOLEH
    | Superadmin -> DITOLAK
    |
    */

    Route::middleware('role:guru')->group(function () {


        /*
        |--------------------------------------------------------------------------
        | Monitoring Attendance
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/attendance/monitoring',
            [AttendanceController::class, 'monitoring']
        )->name('monitoring');


        Route::get(
            '/attendance/monitoring/kelas/{kelas}',
            [AttendanceController::class, 'monitoringKelas']
        )->name('monitoring.kelas');


        /*
        |--------------------------------------------------------------------------
        | Data Attendance
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/getattendance',
            [AttendanceController::class, 'getattendance']
        )->name('getattendance');


        Route::post(
            '/getattendancekelas',
            [AttendanceController::class, 'getattendancekelas']
        )->name('getattendancekelas');


        /*
        |--------------------------------------------------------------------------
        | Peta / Lokasi Attendance
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/tampilkanpeta',
            [AttendanceController::class, 'tampilkanpeta']
        )->name('tampilkanpeta');


        /*
        |--------------------------------------------------------------------------
        | LAPORAN ATTENDANCE - KHUSUS GURU
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/attendance/laporan',
            [AttendanceController::class, 'halamanlaporan']
        )->name('attendance.laporan');


        Route::post(
            '/attendance/cetaklaporan',
            [AttendanceController::class, 'cetaklaporan']
        )->name('attendance.cetaklaporan');


        Route::get(
            '/attendance/rekap',
            [AttendanceController::class, 'rekap']
        )->name('attendance.rekap');


        Route::post(
            '/attendance/cetakrekap',
            [AttendanceController::class, 'cetakrekap']
        )->name('attendance.cetakrekap');


        /*
        |--------------------------------------------------------------------------
        | IZIN / SAKIT - KHUSUS GURU
        |--------------------------------------------------------------------------
        |
        | Guru memeriksa pengajuan izin/sakit siswa sebelum memberikan
        | persetujuan.
        |
        */

        Route::get(
            '/attendance/izinsakit/kelas/{kelas}',
            [AttendanceController::class, 'listIzinSakitKelas']
        )->name('attendance.izinsakit.kelas');


        Route::get(
            '/attendance/izinsakit',
            [AttendanceController::class, 'jurusanIzin']
        )->name('attendance.izinsakit');


        Route::post(
            '/attendance/approveizinsakit',
            [AttendanceController::class, 'approveizinsakit']
        )->name('attendance.approveizinsakit');


        Route::get(
            '/attendance/{id}/batalkanizinsakit',
            [AttendanceController::class, 'batalkanizinsakit']
        )->name('attendance.batalkanizinsakit');
    });


    /*
    |--------------------------------------------------------------------------
    | UPDATE PASSWORD
    |--------------------------------------------------------------------------
    |
    | Tetap dapat digunakan oleh user yang sudah login.
    |
    */

    Route::post(
        '/attendance/updatePassword',
        [AttendanceController::class, 'updatePassword']
    )->name('attendance.updatePassword');


    /*
    |--------------------------------------------------------------------------
    | CEK PENGAJUAN IZIN
    |--------------------------------------------------------------------------
    |
    | Route ini dipertahankan sesuai kode sebelumnya.
    |
    */

    Route::post(
        '/attendance/cekpengajuanizin',
        [AttendanceController::class, 'cekpengajuanizin']
    )->name('attendance.cekpengajuanizin');


    /*
    |--------------------------------------------------------------------------
    | KONFIGURASI - KHUSUS SUPERADMIN
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:superadmin')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Lokasi Sekolah
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/konfigurasi/lokasisekolah',
            [KonfigurasiController::class, 'lokasisekolah']
        )->name('konfigurasi.lokasisekolah');


        Route::post(
            '/konfigurasi/updatelokasisekolah',
            [KonfigurasiController::class, 'updatelokasisekolah']
        )->name('konfigurasi.updatelokasisekolah');


        /*
        |--------------------------------------------------------------------------
        | Waktu Presensi
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/konfigurasi/waktu',
            [KonfigurasiController::class, 'konfigurasiWaktu']
        )->name('konfigurasi.waktu');


        Route::post(
            '/konfigurasi/updatewaktu',
            [KonfigurasiController::class, 'updateWaktu']
        )->name('konfigurasi.updatewaktu');
    });


    /*
    |--------------------------------------------------------------------------
    | SETTING ADMIN
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/panel/setting',
        [AdminController::class, 'setting']
    );

    Route::post(
        '/panel/setting/update',
        [AdminController::class, 'updateSetting']
    );
});