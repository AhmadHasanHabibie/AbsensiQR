<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;


/*
|--------------------------------------------------------------------------
| Admin Controller
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ClassQrController;
use App\Http\Controllers\Admin\OperatorController;
use App\Http\Controllers\Admin\GuruPiketController;
use App\Http\Controllers\Admin\LoginHistoryController as AdminLoginHistoryController;
use App\Http\Controllers\Admin\ActivityLogController as AdminActivityLogController;
use App\Http\Controllers\Admin\SecurityCenterController as AdminSecurityCenterController;
use App\Http\Controllers\Admin\BlockedIpController as AdminBlockedIpController;
use App\Http\Controllers\Admin\EmergencyAuditController as AdminEmergencyAuditController;
use App\Http\Controllers\Admin\PinController as AdminPinController;
use App\Http\Controllers\Operator\DashboardController as OperatorDashboardController;
use App\Http\Controllers\Operator\ScanController as OperatorScanController;
use App\Http\Controllers\Operator\LateController as OperatorLateController;
use App\Http\Controllers\Operator\EmergencyController as OperatorEmergencyController;
use App\Http\Controllers\Piket\DashboardController as PiketDashboardController;
use App\Http\Controllers\Piket\MonitoringController as PiketMonitoringController;
use App\Http\Controllers\Piket\LateController as PiketLateController;
use App\Http\Controllers\Piket\ReportController as PiketReportController;
use App\Http\Controllers\Piket\PlaceholderController as PiketPlaceholderController;
use App\Http\Controllers\Piket\LoginHistoryController as PiketLoginHistoryController;
use App\Http\Controllers\Guru\InternalMailboxController as GuruInternalMailboxController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\MonitoringController as SuperAdminMonitoringController;
use App\Http\Controllers\SuperAdmin\ServerInfoController as SuperAdminServerInfoController;
use App\Http\Controllers\SuperAdmin\BackupController as SuperAdminBackupController;
use App\Http\Controllers\SuperAdmin\MaintenanceController as SuperAdminMaintenanceController;
use App\Http\Controllers\SuperAdmin\ConfigController as SuperAdminConfigController;
use App\Http\Controllers\SuperAdmin\ActivityLogController as SuperAdminActivityLogController;
use App\Http\Controllers\SuperAdmin\AboutController as SuperAdminAboutController;
use App\Http\Controllers\SuperAdmin\PinController as SuperAdminPinController;
use App\Http\Controllers\SuperAdmin\AcademicCalendarController as SuperAdminAcademicCalendarController;
use App\Http\Controllers\SuperAdmin\AttendanceOperationController as SuperAdminAttendanceOperationController;


/*
|--------------------------------------------------------------------------
| Guru Controller
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Guru\ScanController;
use App\Http\Controllers\Guru\AttendanceController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Guru\ScanController as GuruScanController;
use App\Http\Controllers\Guru\LateController as GuruLateController;
use App\Http\Controllers\Guru\ReportController as GuruReportController;
use App\Http\Controllers\Guru\MailboxController as GuruMailboxController;
use App\Http\Controllers\Guru\LoginHistoryController as GuruLoginHistoryController;
use App\Http\Controllers\Admin\MailboxController as AdminMailboxController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\HistoryController as SiswaHistoryController;
use App\Http\Controllers\Siswa\MailboxController as SiswaMailboxController;

/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/login');



/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {


    Route::controller(LoginController::class)->group(function () {


        Route::get(
            '/login',
            'index'
        )->name('login');


        Route::post(
            '/login',
            'login'
        )->name('login.process');


    });


});



Route::middleware('auth')->group(function () {


    Route::post(
        '/logout',
        [LoginController::class,'logout']
    )->name('logout');


});





/*
|--------------------------------------------------------------------------
| SUPER ADMINISTRATOR (SYSTEM OWNER)
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'super_admin'
])
->prefix('superadmin')
->name('superadmin.')
->group(function () {

    Route::get('/verify-pin', [SuperAdminPinController::class, 'showVerifyForm'])->name('pin.verify');
    Route::post('/verify-pin', [SuperAdminPinController::class, 'processVerify'])->name('pin.process');

    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    Route::post('/toggle-testing-mode', [SuperAdminDashboardController::class, 'toggleTestingMode'])->name('toggle-testing-mode');
    Route::get('/monitoring', [SuperAdminMonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('/server-info', [SuperAdminServerInfoController::class, 'index'])->name('server-info.index');

    Route::get('/backup', [SuperAdminBackupController::class, 'index'])->name('backup.index');
    Route::post('/backup', [SuperAdminBackupController::class, 'store'])->name('backup.store');
    Route::get('/backup/{id}/download', [SuperAdminBackupController::class, 'download'])->name('backup.download');

    Route::get('/maintenance', [SuperAdminMaintenanceController::class, 'index'])->name('maintenance.index');
    Route::post('/maintenance/toggle', [SuperAdminMaintenanceController::class, 'toggle'])->name('maintenance.toggle');

    Route::get('/config', [SuperAdminConfigController::class, 'index'])->name('config.index');
    Route::get('/activity-log', [SuperAdminActivityLogController::class, 'index'])->name('activity-log.index');
    Route::get('/about', [SuperAdminAboutController::class, 'index'])->name('about.index');

    /*
    |--------------------------------------------------------------------------
    | Kalender Akademik
    |--------------------------------------------------------------------------
    */

    // Urutan penting: route statis (template, activate-year) harus di atas route {id}
    Route::get('/academic-calendar/template/download', [SuperAdminAcademicCalendarController::class, 'downloadTemplate'])->name('academic-calendar.template');
    Route::post('/academic-calendar/activate-year', [SuperAdminAcademicCalendarController::class, 'activateYear'])->name('academic-calendar.activate-year');
    Route::post('/academic-calendar/import', [SuperAdminAcademicCalendarController::class, 'import'])->name('academic-calendar.import');
    Route::get('/academic-calendar/{id}', [SuperAdminAcademicCalendarController::class, 'show'])->name('academic-calendar.show')->where('id', '[0-9]+');
    Route::get('/academic-calendar', [SuperAdminAcademicCalendarController::class, 'index'])->name('academic-calendar.index');

    /*
    |--------------------------------------------------------------------------
    | Operasional Absensi & Emergency Override
    |--------------------------------------------------------------------------
    */
    Route::get('/attendance-operation', [SuperAdminAttendanceOperationController::class, 'index'])->name('attendance-operation.index');
    Route::post('/attendance-operation/toggle', [SuperAdminAttendanceOperationController::class, 'toggle'])->name('attendance-operation.toggle');

});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:admin'
])

->prefix('admin')

->name('admin.')

->group(function(){

    Route::get('/verify-pin', [AdminPinController::class, 'showVerifyForm'])->name('pin.verify');
    Route::post('/verify-pin', [AdminPinController::class, 'processVerify'])->name('pin.process');



    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [AdminDashboardController::class,'index']
    )
    ->name('dashboard');

    Route::get(
        '/dashboard/class/{id}',
        [AdminDashboardController::class, 'classDetail']
    )
    ->name('dashboard.class-detail');




  /*
|--------------------------------------------------------------------------
| Master Data Guru
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Import Guru
|--------------------------------------------------------------------------
*/

Route::get(
    'guru/import',
    [GuruController::class, 'importForm']
)
->name('guru.import.form');

Route::post(
    'guru/import',
    [GuruController::class, 'import']
)
->name('guru.import');


/*
|--------------------------------------------------------------------------
| Download Template Guru
|--------------------------------------------------------------------------
*/

Route::get(
    'guru/template',
    [GuruController::class, 'downloadTemplate']
)
->name('guru.template');


/*
|--------------------------------------------------------------------------
| Resource Guru
|--------------------------------------------------------------------------
*/

Route::resource(
    'guru',
    GuruController::class
)->except(['destroy']);

    Route::resource(
        'kelas',
        KelasController::class
    )->except(['destroy']);


    /*
    |--------------------------------------------------------------------------
    | Import Siswa
    |--------------------------------------------------------------------------
    */

    Route::get(
        'siswa/import',
        [SiswaController::class, 'importForm']
    )
    ->name('siswa.import.form');

    Route::post(
        'siswa/import',
        [SiswaController::class, 'import']
    )
    ->name('siswa.import');


    /*
    |--------------------------------------------------------------------------
    | Download Template Siswa
    |--------------------------------------------------------------------------
    */

    Route::get(
        'siswa/template',
        [SiswaController::class, 'downloadTemplate']
    )
    ->name('siswa.template');


    Route::resource(
        'siswa',
        SiswaController::class
    )->except(['destroy']);

    /*
    |--------------------------------------------------------------------------
    | Import Operator
    |--------------------------------------------------------------------------
    */

    Route::get(
        'operator/import',
        [OperatorController::class, 'importForm']
    )->name('operator.import.form');

    Route::post(
        'operator/import',
        [OperatorController::class, 'import']
    )->name('operator.import');

    Route::get(
        'operator/template',
        [OperatorController::class, 'downloadTemplate']
    )->name('operator.template');

    Route::resource(
        'operator',
        OperatorController::class
    )->except(['destroy']);

    /*
    |--------------------------------------------------------------------------
    | Import Guru Piket
    |--------------------------------------------------------------------------
    */

    Route::get(
        'guru-piket/import',
        [GuruPiketController::class, 'importForm']
    )->name('guru-piket.import.form');

    Route::post(
        'guru-piket/import',
        [GuruPiketController::class, 'import']
    )->name('guru-piket.import');

    Route::get(
        'guru-piket/template',
        [GuruPiketController::class, 'downloadTemplate']
    )->name('guru-piket.template');

    Route::resource(
        'guru-piket',
        GuruPiketController::class
    )->except(['destroy']);



    /*
    |--------------------------------------------------------------------------
    | Download QR Siswa
    |--------------------------------------------------------------------------
    */


    Route::get(
        'siswa/{id}/download-qr',
        [SiswaController::class,'downloadQr']
    )
    ->name('siswa.downloadQr');





    /*
    |--------------------------------------------------------------------------
    | LAPORAN ABSENSI ADMIN
    |--------------------------------------------------------------------------
    */


    Route::controller(ReportController::class)
    ->prefix('laporan')
    ->name('laporan.')
    ->group(function(){



        /*
        |--------------------------------------------------------------------------
        | Halaman Laporan
        |--------------------------------------------------------------------------
        */


        Route::get(
            '/',
            'index'
        )
        ->name('index');




        /*
        |--------------------------------------------------------------------------
        | Export PDF
        |--------------------------------------------------------------------------
        */


        Route::get(
            '/pdf',
            'exportPdf'
        )
        ->name('pdf');




        /*
        |--------------------------------------------------------------------------
        | Export Excel
        |--------------------------------------------------------------------------
        */


        Route::get(
            '/excel',
            'exportExcel'
        )
        ->name('excel');



    });

    /*
    |--------------------------------------------------------------------------
    | Mailbox Admin
    |--------------------------------------------------------------------------
    */

    Route::get('/mailbox', [AdminMailboxController::class, 'index'])->name('mailbox.index');
    Route::get('/mailbox/{id}/download', [AdminMailboxController::class, 'download'])->name('mailbox.download');

    /*
    |--------------------------------------------------------------------------
    | Download QR Code Siswa Per Kelas (Admin)
    |--------------------------------------------------------------------------
    */

    Route::get('/qr-siswa', [ClassQrController::class, 'index'])->name('qr-siswa.index');
    Route::get('/qr-siswa/pdf', [ClassQrController::class, 'exportPdf'])->name('qr-siswa.pdf');

    Route::get('/login-history', [AdminLoginHistoryController::class, 'index'])->name('login-history.index');
    Route::get('/activity-log', [AdminActivityLogController::class, 'index'])->name('activity-log.index');

    Route::get('/security-center', [AdminSecurityCenterController::class, 'index'])->name('security-center.index');
    Route::get('/security-center/data', [AdminSecurityCenterController::class, 'data'])->name('security-center.data');

    Route::get('/blocked-ips', [AdminBlockedIpController::class, 'index'])->name('blocked-ips.index');
    Route::patch('/blocked-ips/{blockedIp}/unblock', [AdminBlockedIpController::class, 'unblock'])->name('blocked-ips.unblock');
    Route::patch('/blocked-ips/{blockedIp}/permanent', [AdminBlockedIpController::class, 'makePermanent'])->name('blocked-ips.permanent');

    Route::get('/emergency-audit', [AdminEmergencyAuditController::class, 'index'])->name('emergency-audit.index');
    Route::get('/emergency-audit/pdf', [AdminEmergencyAuditController::class, 'exportPdf'])->name('emergency-audit.pdf');
    Route::get('/emergency-audit/excel', [AdminEmergencyAuditController::class, 'exportExcel'])->name('emergency-audit.excel');
    Route::get('/emergency-audit/csv', [AdminEmergencyAuditController::class, 'exportCsv'])->name('emergency-audit.csv');

    /*
    |--------------------------------------------------------------------------
    | Kalender Akademik (Read Only)
    |--------------------------------------------------------------------------
    */
    Route::get('/academic-calendar/{id}', [SuperAdminAcademicCalendarController::class, 'show'])->name('academic-calendar.show')->where('id', '[0-9]+');
    Route::get('/academic-calendar', [SuperAdminAcademicCalendarController::class, 'index'])->name('academic-calendar.index');

});







/*
|--------------------------------------------------------------------------
| GURU
|--------------------------------------------------------------------------
*/


Route::middleware([
    'auth',
    'role:teacher'
])

->prefix('guru')

->name('guru.')

->group(function(){



    /*
    |--------------------------------------------------------------------------
    | Dashboard Guru
    |--------------------------------------------------------------------------
    */


    Route::get(
        '/dashboard',
        [GuruDashboardController::class,'index']
    )
    ->name('dashboard');




    /*
    |--------------------------------------------------------------------------
    | Scan QR
    |--------------------------------------------------------------------------
    */


    Route::controller(ScanController::class)
    ->group(function(){


        Route::get(
            '/scan',
            'index'
        )
        ->name('scan.index');


        Route::post(
            '/scan',
            'store'
        )
        ->middleware('throttle:scan-qr')
        ->name('scan.store');


    });




    /*
    |--------------------------------------------------------------------------
    | Terlambat
    |--------------------------------------------------------------------------
    */

    Route::controller(GuruLateController::class)
    ->prefix('terlambat')
    ->name('terlambat.')
    ->group(function () {

        Route::get('/', 'index')->name('index');

        Route::get('/create', 'create')->name('create');

        Route::post('/', 'store')->name('store');
    });




    /*
    |--------------------------------------------------------------------------
    | Attendance
    |--------------------------------------------------------------------------
    */


    Route::controller(AttendanceController::class)
    ->group(function(){


        Route::get(
            '/attendance',
            'index'
        )
        ->name('attendance.index');


        Route::post(
            '/attendance/confirm',
            'confirm'
        )
        ->name('attendance.confirm');


        Route::post(
            '/attendance/update-status',
            'updateStatus'
        )
        ->name('attendance.update-status');


    });




    /*
    |--------------------------------------------------------------------------
    | Laporan Absensi Guru
    |--------------------------------------------------------------------------
    */

    Route::controller(GuruReportController::class)
    ->prefix('laporan')
    ->name('laporan.')
    ->group(function () {

        Route::get('/', 'index')->name('index');

        Route::get('/export/pdf', 'exportPdf')->name('pdf');

        Route::get('/export/excel', 'exportExcel')->name('excel');
    });




    /*
    |--------------------------------------------------------------------------
    | Profil
    |--------------------------------------------------------------------------
    */


    Route::view(
        '/profil',
        'Guru.Profil.Index'
    )
    ->name('profil.index');



    /*
    |--------------------------------------------------------------------------
    | Mailbox Guru
    |--------------------------------------------------------------------------
    */

    Route::get('/mailbox/create/{student}', [GuruMailboxController::class, 'create'])->name('mailbox.create');
    Route::post('/mailbox', [GuruMailboxController::class, 'store'])->name('mailbox.store');

    Route::get('/internal-mailbox', [GuruInternalMailboxController::class, 'index'])->name('mailbox.index');
    Route::get('/internal-mailbox/{id}', [GuruInternalMailboxController::class, 'show'])->name('mailbox.show');

    Route::get('/login-history', [GuruLoginHistoryController::class, 'index'])->name('login-history.index');

    /*
    |--------------------------------------------------------------------------
    | Kalender Akademik (Read Only)
    |--------------------------------------------------------------------------
    */
    Route::get('/academic-calendar/{id}', [SuperAdminAcademicCalendarController::class, 'show'])->name('academic-calendar.show')->where('id', '[0-9]+');
    Route::get('/academic-calendar', [SuperAdminAcademicCalendarController::class, 'index'])->name('academic-calendar.index');

});







/*
|--------------------------------------------------------------------------
| SISWA
|--------------------------------------------------------------------------
*/


Route::middleware([
    'auth',
    'role:student'
])

->prefix('siswa')

->name('siswa.')

->group(function(){



    /*
    |--------------------------------------------------------------------------
    | Dashboard Siswa
    |--------------------------------------------------------------------------
    */


    Route::get(
        '/dashboard',
        [SiswaDashboardController::class,'index']
    )
    ->name('dashboard');





    /*
    |--------------------------------------------------------------------------
    | Riwayat Absensi Siswa
    |--------------------------------------------------------------------------
    */


    Route::get(
        '/riwayat',
        [SiswaHistoryController::class,'index']
    )
    ->name('riwayat.index');



    /*
    |--------------------------------------------------------------------------
    | Mailbox Siswa
    |--------------------------------------------------------------------------
    */

    Route::get('/mailbox', [SiswaMailboxController::class, 'index'])->name('mailbox.index');
    Route::get('/mailbox/{id}', [SiswaMailboxController::class, 'show'])->name('mailbox.show');
    Route::get('/mailbox/{id}/download', [SiswaMailboxController::class, 'download'])->name('mailbox.download');

});

/*
|--------------------------------------------------------------------------
| OPERATOR
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'operator'
])
->prefix('operator')
->name('operator.')
->group(function(){

    Route::get(
        '/dashboard',
        [OperatorDashboardController::class, 'index']
    )->name('dashboard');

    Route::get(
        '/profil',
        [OperatorDashboardController::class, 'profil']
    )->name('profil.index');

    Route::controller(OperatorScanController::class)
    ->prefix('scan')
    ->name('scan.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->middleware('throttle:scan-qr')->name('store');
    });

    Route::controller(OperatorLateController::class)
    ->prefix('terlambat')
    ->name('terlambat.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show');
    });

    Route::controller(OperatorEmergencyController::class)
    ->prefix('emergency')
    ->name('emergency.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
    });

});

/*
|--------------------------------------------------------------------------
| GURU PIKET
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:piket'
])
->prefix('piket')
->name('piket.')
->group(function () {

    Route::get('/dashboard', [PiketDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil', [PiketDashboardController::class, 'profil'])->name('profil.index');

    Route::get('/monitoring', [PiketMonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/export/pdf', [PiketMonitoringController::class, 'exportPdf'])->name('monitoring.pdf');
    Route::get('/monitoring/export/excel', [PiketMonitoringController::class, 'exportExcel'])->name('monitoring.excel');
    Route::get('/monitoring/{id}', [PiketMonitoringController::class, 'show'])->name('monitoring.show');
    Route::post('/monitoring/reminder', [PiketMonitoringController::class, 'sendReminder'])->name('monitoring.send-reminder');
    Route::get('/terlambat', [PiketLateController::class, 'index'])->name('terlambat.index');
    Route::get('/terlambat/{id}', [PiketLateController::class, 'show'])->name('terlambat.show');

    Route::get('/laporan', [PiketReportController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/class/{id}', [PiketReportController::class, 'show'])->name('laporan.show');
    Route::get('/laporan/export/pdf', [PiketReportController::class, 'exportPdf'])->name('laporan.pdf');
    Route::get('/laporan/export/excel', [PiketReportController::class, 'exportExcel'])->name('laporan.excel');
    Route::get('/mailbox', [PiketPlaceholderController::class, 'mailbox'])->name('mailbox.index');

    Route::get('/login-history', [PiketLoginHistoryController::class, 'index'])->name('login-history.index');

    /*
    |--------------------------------------------------------------------------
    | Kalender Akademik (Read Only)
    |--------------------------------------------------------------------------
    */
    Route::get('/academic-calendar/{id}', [SuperAdminAcademicCalendarController::class, 'show'])->name('academic-calendar.show')->where('id', '[0-9]+');
    Route::get('/academic-calendar', [SuperAdminAcademicCalendarController::class, 'index'])->name('academic-calendar.index');

});


