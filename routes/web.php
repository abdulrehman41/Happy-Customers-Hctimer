<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\SickLeaveController;
use App\Http\Controllers\Admin\LeaveController;
use App\Http\Controllers\Admin\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Route::get('/clear-cache', function() {
//     Artisan::call('cache:clear');
//         Artisan::call('config:clear');
//         // Artisan::call('view:clear');

// Artisan::call('config:cache');
//     return "all  is cleared";
// });
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Route::get('/profile', function () {
//     return 'hi';
// });
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');



Route::prefix('admin')->middleware('admin','blockIp')->namespace('App\\Http\\Controllers\\Admin')->group(function () {
    Route::get('dashboard/clock', 'AdminController@clock')->name('admin.clock');
    Route::post('start-time', 'AdminController@starttime')->name('admin.starttime');
    Route::post('end-time', 'AdminController@endtime')->name('admin.endtime');
    Route::get('your/attendance_history', 'AdminController@user_attendance_history')->name('admin.attendance_history');

    Route::get('dashboard', 'AdminController@dashboard')->name('admin.dashboard');
    Route::get('/loan', 'AdminController@ViewLoan')->name('admin.loan');
    Route::get('add_loan', 'AdminController@AddLoanView')->name('admin.addloan');
    Route::get('pause_loan/{id}', 'AdminController@PauseLoan')->name('admin.pauseloan');
    Route::get('start_loan/{id}', 'AdminController@StartLoan')->name('admin.startloan');
    Route::get('/stop_loan/{id}/{s_date}', 'AdminController@StopLoan')->name('admin.stoploan');
    Route::get('/delete_loan/{id}/{s_date}', 'AdminController@DeleteLoan');
    
    Route::post('add_loan_data', 'AdminController@AddLoanData')->name('admin.addloandata');
    Route::get('department/permission', 'AdminController@deperpermission')->name('admin.department.permission');
        Route::post('department-permission/{id}', 'AdminController@deperpermissionsave')->name('admin.department.save');
        Route::get('update-department-permission/{id}', 'AdminController@deperpermissionupdate')->name('admin.update.department');
        Route::get('update-department-neew/{id}', 'AdminController@departmentpostper');

    Route::get('employees', 'AdminController@employees')->name('admin.employees');
    Route::get('department', 'AdminController@department')->name('admin.department');
    Route::get('employee/create', 'AdminController@employeeCreate')->name('admin.employees.create');
    Route::get('employee/{id}/view', 'AdminController@employeeView')->name('admin.employees.view');
    Route::get('employee/{id}/edit', 'AdminController@employeeEdit')->name('admin.employees.edit');
    Route::get('employee/{id}/delete', 'AdminController@employeeDestroy')->name('admin.employees.delete');
    
    Route::get('department_employe', 'AdminController@department_employe')->name('admin.employe.department');
    // Route::get('employee/{id}/show', 'AdminController@employeeShow')->name('admin.employees.show');
    Route::post('employee/{id}/update', 'AdminController@employeeUpdate')->name('admin.employeeUpdate');
    Route::post('employees', 'AdminController@employeeStore')->name('admin.employeeStore');
    Route::get('attendance_history','AdminController@attendance_history')->name('admin.attendance_history');
    Route::get('attendance/search', 'AdminController@attendance_search')->name('admin.attendance_search');
    Route::post('admin/add_multi/attendance', 'AdminController@admin_multi_attendance')->name('admin.add_multi.admin_attendance');
    
    Route::post('admin/add/attendance', 'AdminController@admin_attendance')->name('admin.add.admin_attendance');
    Route::get('admin/role/{id}', 'AdminController@Roledelete')->name('admin.delete_role');
    
    Route::get('email_all', [PayrollController::class, 'email_all']);
    
    
    Route::get('add-attendace', 'AdminController@addattendance');
    Route::get('add-multi-attendace', 'AdminController@add_multi_attendance');
    Route::post('get-user-dropdown', 'AdminController@getuserdropdown');
            
    Route::post('hourly_print',[PayrollController::class,'HourlyPrint']);
    Route::post('single_email',[PayrollController::class,'SingleEmail'])->name('admin.single.email');
    Route::get('daily_single_email',[PayrollController::class,'DailySingleEmail']);
    Route::post('daily_print',[PayrollController::class,'DailyPrint']);
    Route::post('generate-pdf',[PayrollController::class,'GeneratePDF'])->name('admin.generate.pdf');
    
    Route::get('attent_status_disapprove/{id}', 'AdminController@attent_status_disapprove')->name('admin.attent_status_disapprove');
    Route::get('attent_status_approve/{id}', 'AdminController@attent_status_approve')->name('admin.attent_status_approve');
    Route::get('delete_bonus/{id}/{start_date}/{end_date}','AdminController@deleteBonus')->name('admin.delete_bonus');
    Route::get('delete_attendance/{id}/{date}', 'AdminController@delete_attendance')->name('admin.delete_attendance');
    Route::get('update_attendance/{id}', 'AdminController@update_attendance')->name('admin.update_attendance');
    Route::get('update_attendance_return/{id}/{userId}', 'AdminController@update_attendance_return')->name('admin.update_attendance_return');


    Route::post('add_department', 'AdminController@add_department')->name('admin.add_department');
    Route::get('depart_status_deactive/{id}', 'AdminController@depart_status_deactive')->name('admin.depart_status_deactive');
    Route::get('depart_status_active/{id}', 'AdminController@depart_status_active')->name('admin.depart_status_active');
    Route::get('delete_department/{id}', 'AdminController@delete_department')->name('admin.delete_department');
    Route::post('edit_department/{id}', 'AdminController@edit_department')->name('admin.edit_department');
    Route::get('depart_user/{id}', 'AdminController@userdepartment')->name('admin.depart_user');

    ///Profile Section /////////////
    // Route::post('profile', 'AdminController@profile')->name('profile');
    Route::get('update_profile', 'AdminController@update_profile')->name('admin.update_profile');


    Route::any('/add_threshold', 'AdminController@add_threshold');
    Route::post('/add/deduction', 'AdminController@create_deduction')->name('add.deduction');


    Route::get('/updateThreshold', 'AdminController@updateThreshold')->name('update.threshold');



    Route::get('/threshold', [AdminController::class, 'threshold']);
    Route::get('/accumulate_threshold', [AdminController::class, 'accumulate_threshold']);
    Route::get('/monthly_accumulate_threshold', [AdminController::class, 'monthly_accumulate_threshold']);
    Route::get('/update_acc_threshold', [AdminController::class, 'update_acc_threshold']);
    Route::get('/update_monthly_acc_threshold', [AdminController::class, 'update_monthly_acc_threshold']);
    Route::get('/delete_accumulate_threshold/{payroll_no}', [AdminController::class, 'delete_accumulate_threshold']);
    Route::get('/delete_monthly_accumulate_threshold/{payroll_no}', [AdminController::class, 'delete_monthly_accumulate_threshold']);
    Route::get('/one_time_deduction', [AdminController::class, 'one_time_deduction']);
    Route::get('/add_one_time_deduction', [AdminController::class, 'add_one_time_deduction']);
    Route::get('/delete_one_time_deduction/{id}/{s_period}', [AdminController::class, 'delete_one_time_deduction']);
    Route::get('/edit_one_time_deduction/{id}/{s_period}', [AdminController::class, 'edit_one_time_deduction']);
    Route::post('admin/submit_edit_one_time_deduction', [AdminController::class, 'submit_edit_one_time_deduction'])->name('admin.edit.onetimededuction');
    Route::post('admin/submit_one_time_deduction', [AdminController::class, 'submit_one_time_deduction'])->name('admin.add.onetimededuction');
    
    Route::get('/continuous_deduction', [AdminController::class, 'continuous_deduction']);
    Route::get('/add_continuous_deduction', [AdminController::class, 'add_continuous_deduction']);
    Route::get('/delete_continuous/{id}/{s_date}',[AdminController::class,'delete_continuous']);
    Route::post('admin/submit_continuous_deduction', [AdminController::class, 'submit_continuous_deduction'])->name('admin.add.continuous');
    Route::get('/stop_continuous/{id}/{s_period}', [AdminController::class, 'stop_continuous']);
    Route::get('/start_continuous/{id}/{s_period}', [AdminController::class, 'start_continuous']);
    
    Route::post('/add_threshold', [AdminController::class, 'add_threshold']);
    Route::get('/add_deduction', [AdminController::class, 'add_deduction']);
    Route::get('/edit_threshold/{id}', [AdminController::class, 'edit_threshold']);
    Route::get('/edit_deduction/{id}', [AdminController::class, 'edit_deduction']);

    Route::post('/update_threshold/{id}', [AdminController::class, 'update_threshold']);
    Route::post('/update_deduction/{id}', [AdminController::class, 'update_deduction']);

    Route::get('/delete_threshold/{id}', [AdminController::class, 'delete_threshold']);
    Route::get('/deduction', 'AdminController@deduction')->name('deduction');
    Route::get('processedPayroll', 'AdminController@processedPayroll')->name('processedPayroll');
    Route::get('search_process_payroll', 'AdminController@searchProcessPayroll')->name('admin.process_payroll_search');
    Route::get('/delete_process_payroll/{id}', 'AdminController@deleteProcessPayroll');


    Route::get('/sick-leave', 'LeaveController@sick_leave');
    Route::post('/insert_sick_leave', 'LeaveController@insert_sick_leave');
    Route::get('/sick_status_deactive/{id}', 'LeaveController@sick_status_deactive');
    Route::get('/sick_status_active/{id}', 'LeaveController@sick_status_active');
    Route::get('/delete_sick/{id}', 'LeaveController@delete_sick');
    ///////sick leave end
    ///////vacation leave start
    Route::get('/vacation-leave', 'LeaveController@vacation_leave');
    Route::post('/insert_vacation_leave', 'LeaveController@insert_vacation_leave');
    Route::get('/vacation_status_deactive/{id}', 'LeaveController@vacation_status_deactive');
    Route::get('/vacation_status_active/{id}', 'LeaveController@vacation_status_active');
    Route::get('/delete_vacation/{id}', 'LeaveController@delete_vacation');
    ///////vacation leave end

    //Holiday
    Route::get('/holidays', 'LeaveController@holidays');
    Route::get('/add_holiday', 'LeaveController@add_holiday');
    Route::post('/add_holiday_values', 'LeaveController@add_holiday_values');
    Route::get('/delete_holiday/{id}', 'LeaveController@delete_holiday');
    Route::get('/update_holiday/{id}', 'LeaveController@update_holiday');
    Route::get('/update_holiday_values/{id}', 'LeaveController@update_holiday_values');
    //End Holiday
    //Maternity
    Route::get('/maternity', 'LeaveController@MaternityLeave');
    Route::post('/insert_maternity_leave', 'LeaveController@InsertMaternity');
    Route::get('/delete_maternity/{id}', 'LeaveController@DeleteMaternity');
    Route::get('/approve_maternity/{id}', 'LeaveController@ApproveMaternity');

    //End Matenity

    Route::get('/threshold', [AdminController::class, 'threshold']);
    Route::post('/add_threshold', [AdminController::class, 'add_threshold']);
    Route::get('/edit_threshold/{id}', [AdminController::class, 'edit_threshold']);
    Route::post('/update_threshold/{id}', [AdminController::class, 'update_threshold']);
    Route::get('/delete_threshold/{id}', [AdminController::class, 'delete_threshold']);
    Route::get('deduction', 'AdminController@deduction')->name('deduction');
    Route::post('/admin/logout', [AdminController::class, 'Adminlogout'])->name('admin.logout');
    Route::get('/ip_managment', [AdminController::class, 'IpManagment']);
    Route::post('/ip_add', [AdminController::class, 'AddIP']);
    Route::post('/ip_edit/{id}', [AdminController::class, 'IpEdit']);
    Route::get('/ip_delete/{id}', [AdminController::class, 'IpDelete'])->name('admin.ip.delete');
    Route::match(['get','post'],'/mac_address_managment', [AdminController::class, 'MacAddressManagment']);
    Route::match(['get','post'],'/mac_add', [AdminController::class, 'AddMac']);
    Route::post('/mac_address_edit/{id}', [AdminController::class, 'MacAddressEdit']);
    Route::get('/mac_delete/{id}', [AdminController::class, 'MacDelete'])->name('admin.mac.delete');
    Route::get('permission', 'AdminController@AddAdmin')->name('add.admin.permission');
    Route::get('supervisor_departments', 'AdminController@supervisor_departments')->name('add.supervisor.departments');
    Route::any('permission/{id}', [AdminController::class, 'Adminpermissionupdate'])->name('permission.update');

    Route::any('add/roles', [AdminController::class, 'AddRoles'])->name('add.roles');
    Route::post('update/roles/{id}', [AdminController::class, 'UpdateRoles'])->name('update.roles');

    Route::post('/admin/create/user', [AdminController::class, 'AdmincreateUser'])->name('admin.create.user');
    //Report
    Route::get('deduction-report', 'AdminController@DeductionReport');
    Route::post('deduction_response', [AdminController::class, 'DeductionResponse']);
    //End Report
});

//get ip
Route::get('get_ip',[PayrollController::class,'getIp']);
Route::get('get_mac',[PayrollController::class,'getMac']);
    
Route::post('/admin/logout', [AdminController::class, 'Adminlogout'])->name('admin.logout')->middleware('admin','blockIp');

//notices
Route::get('admin/notices', [NoticeController::class, 'Notices'])->middleware('admin','blockIp');
Route::get('admin/notice/{id}', [NoticeController::class, 'NoticesDelete'])->middleware('admin','blockIp');
Route::post('admin/add/notices', [NoticeController::class, 'AddNotices'])->middleware('admin','blockIp');
Route::post('admin/edit/notices', [NoticeController::class, 'EditNotices'])->middleware('admin','blockIp');

Route::get('admin/bonus', [PayrollController::class, 'Addbonus'])->name('bonus')->middleware('admin','blockIp');
Route::post('store/Bonus', [PayrollController::class, 'storeBonus'])->name('storeboubus')->middleware('admin','blockIp');
Route::get('admin/viewbonus', [PayrollController::class, 'ViewBonus'])->name('viewbonus')->middleware('admin','blockIp');
Route::get('admin/edit_bonus/{id}/{start}/{end}', [PayrollController::class, 'EditBonus'])->name('editbonus')->middleware('admin','blockIp');
Route::get('admin/update_bonus', [PayrollController::class, 'UpdateBonus'])->name('updatebonus')->middleware('admin','blockIp');

Route::get('temp_tax_deduction/{id}',[PayrollController::class, 'temp_deduction'])->name("admin.temp_deduction")->middleware('admin','blockIp');
Route::get('admin/payroll', [PayrollController::class, 'payroll'])->name('admin-payroll')->middleware('admin','blockIp');
Route::get('admin/daily_payroll', [PayrollController::class, 'daily_payroll'])->middleware('admin','blockIp');
Route::match(['post', 'get'],'admin/search', [PayrollController::class, 'search'])->name('admin-search')->middleware('admin','blockIp');
Route::post('admin/daily_search', [PayrollController::class, 'daily_search'])->middleware('admin','blockIp');
Route::get('atten_get', [PayrollController::class, 'atten_get'])->middleware('admin','blockIp');
Route::get('processed_atten_get',[PayrollController::class,'processed_atten_get'])->middleware('admin','blockIp');
Route::get('email_pass',[PayrollController::class,'email_pass'])->middleware('admin','blockIp');
Route::get('daily_atten_get', [PayrollController::class, 'daily_atten_get'])->middleware('admin','blockIp');
Route::get('admin/proceed', [PayrollController::class, 'payrol_proceed'])->middleware('admin','blockIp');
Route::get('admin/proceed_all', [PayrollController::class, 'payrol_proceed_all'])->middleware('admin','blockIp');
Route::get('admin/daily_proceed', [PayrollController::class, 'daily_proceed'])->middleware('admin','blockIp');
Route::get('admin/payroll_start', [PayrollController::class, 'PayrollStartFunc'])->middleware('admin','blockIp');
Route::get('admin/add_start_date', [PayrollController::class, 'AddStartDate'])->middleware('admin','blockIp');
Route::get('admin/edit_payroll_start/{id}', [PayrollController::class, 'EditStartDate'])->middleware('admin','blockIp');
Route::post('admin/update_payroll_start/{id}', [PayrollController::class, 'UpdateStartDate'])->middleware('admin','blockIp');
Route::post('/filter_attendance', [PayrollController::class, 'filter_attendance'])->middleware('admin','blockIp');
Route::POST('/filter_attendance', 'PayrollController@filter_attendance')->name('filter-attendance')->middleware('admin','blockIp');


Route::post('update_profile', [AdminController::class, 'update_profile'])->middleware('admin','blockIp');

Route::view('/profile', 'Employee/profile')->middleware('employee');
Route::view('/admin/profile', 'Admin/profile')->middleware('admin','blockIp');

Route::prefix('employee')->middleware('employee','blockIp')->namespace('App\\Http\\Controllers\\Employee')->group(function () {

    Route::get('update_profile', 'AdminController@update_profile')->name('update_profile');
    Route::get('dashboard', 'EmployeeController@dashboard')->name('employee.dashboard');
    Route::post('start-time', 'EmployeeController@starttime')->name('employee.starttime');
    Route::post('end-time', 'EmployeeController@endtime')->name('employee.endtime');
    Route::get('attendance_history', 'EmployeeController@attendance_history')->name('employee.attendance_history');
    Route::get('user_processed_payroll', 'EmployeeController@user_processed_payroll')->name('employee.user_processed_payroll');
    Route::get('user_processed_payroll', 'EmployeeController@user_processed_payroll')->name('employee.user_processed_payroll');
    Route::get('notices', 'EmployeeController@Noticesshow')->name('employee.notices');
    Route::get('notices/detail/{id}', 'EmployeeController@Noticesdetail')->name('employee.notices.details');
});



Route::get('employee/sick-leave', [SickLeaveController::class, 'sick_leave'])->middleware('admin','blockIp');
Route::get('employee/vacation-leave', [SickLeaveController::class, 'vacation_leave'])->middleware('admin','blockIp');
Route::post('employee/insert_sick_leave', [SickLeaveController::class, 'insert_sick_leave'])->middleware('admin','blockIp');
Route::post('employee/insert_vacation_leave', [SickLeaveController::class, 'insert_vacation_leave'])->middleware('admin','blockIp');
Route::get('employee/maternity-leave', [SickLeaveController::class, 'MaternityLeave'])->middleware('admin','blockIp');
Route::post('employee/insert_maternity_leave', [SickLeaveController::class, 'InsertMaternityLeave'])->middleware('admin','blockIp');

Route::get('Testmail', 'App\Http\Controllers\TestController@Testmail')->middleware('admin','blockIp');
