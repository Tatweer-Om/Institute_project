<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\UserController;

use App\Http\Controllers\OfferController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AccountController;

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;

use App\Http\Controllers\EnrolController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WinnLosController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/switch-language/{locale}', [HomeController::class, 'switchLanguage'])->name('switch_language');
// category dress

//user
Route::match(['get', 'post'], 'login_page', [UserController::class, 'login_page'])->name('login_page');
Route::match(['get', 'post'], 'login', [UserController::class, 'login'])->name('login');
Route::match(['get', 'post'], 'logout', [UserController::class, 'logout'])->name('logout');
Route::get('user', [UserController::class, 'index'])->name('user');
Route::post('add_user', [UserController::class, 'add_user'])->name('add_user');
Route::get('show_user', [UserController::class, 'show_user'])->name('show_user');
Route::post('edit_user', [UserController::class, 'edit_user'])->name('edit_user');
Route::post('update_user', [UserController::class, 'update_user'])->name('update_user');
Route::post('delete_user', [UserController::class, 'delete_user'])->name('delete_user');


Route::get('expense_category', [ExpenseCategoryController::class, 'index'])->name('expense_category');
Route::post('add_expense_category', [ExpenseCategoryController::class, 'add_expense_category'])->name('add_expense_category');
Route::get('show_expense_category', [ExpenseCategoryController::class, 'show_expense_category'])->name('show_expense_category');
Route::post('edit_expense_category', [ExpenseCategoryController::class, 'edit_expense_category'])->name('edit_expense_category');
Route::post('update_expense_category', [ExpenseCategoryController::class, 'update_expense_category'])->name('update_expense_category');
Route::post('delete_expense_category', [ExpenseCategoryController::class, 'delete_expense_category'])->name('delete_expense_category');

// expense_categoryController Routes

Route::get('expense', [ExpenseController::class, 'index'])->name('expense');
Route::post('add_expense', [ExpenseController::class, 'add_expense'])->name('add_expense');
Route::get('show_expense', [ExpenseController::class, 'show_expense'])->name('show_expense');
Route::post('edit_expense', [ExpenseController::class, 'edit_expense'])->name('edit_expense');
Route::post('update_expense', [ExpenseController::class, 'update_expense'])->name('update_expense');
Route::post('delete_expense', [ExpenseController::class, 'delete_expense'])->name('delete_expense_category');
Route::get('download_expense_image/{id}', [ExpenseController::class, 'download_expense_image'])->name('download_expense_image');


// AccountController Routes

Route::get('account', [AccountController::class, 'index'])->name('account');
Route::post('add_account', [AccountController::class, 'add_account'])->name('add_account');
Route::get('show_account', [AccountController::class, 'show_account'])->name('show_account');
Route::post('edit_account', [AccountController::class, 'edit_account'])->name('edit_account');
Route::post('update_account', [AccountController::class, 'update_account'])->name('update_account');
Route::post('delete_account', [AccountController::class, 'delete_account'])->name('delete_account');


//sms
Route::get('sms', [SmsController::class, 'index'])->name('sms');
Route::post('get_sms_status', [SmsController::class, 'get_sms_status'])->name('get_sms_status');
Route::match(['get', 'post'], 'add_status_sms', [SmsController::class, 'add_status_sms'])->name('add_status_sms');


//Settings
Route::get('setting', [SettingController::class, 'setting'])->name('setting');
Route::post('add_setting', [SettingController::class, 'add_setting'])->name('add_setting');
Route::get('setting_data', [SettingController::class, 'setting_data'])->name('setting_data');
Route::post('dress_avail', [SettingController::class, 'dress_avail'])->name('dress_avail');



//STUDENT

Route::match(['get', 'post'], 'student', [StudentController::class, 'index'])->name('student');
Route::post('add_student', [StudentController::class, 'add_student'])->name('add_student');
Route::get('show_student', [StudentController::class, 'show_student'])->name('show_student');
Route::post('edit_student', [StudentController::class, 'edit_student'])->name('edit_student');
Route::post('update_student', [StudentController::class, 'update_student'])->name('update_student');
Route::post('delete_student', [StudentController::class, 'delete_student'])->name('delete_student');
Route::get('student_profile/{id}', [StudentController::class, 'student_profile'])->name('student_profile');
Route::post('student_profile_data', [StudentController::class, 'student_profile_data'])->name('student_profile_data');
Route::get('show_student_courses', [StudentController::class, 'show_student_courses'])->name('show_student_courses');



//teacher

Route::match(['get', 'post'], 'teacher', [TeacherController::class, 'index'])->name('teacher');
Route::post('add_teacher', [TeacherController::class, 'add_teacher'])->name('add_teacher');
Route::get('show_teacher', [TeacherController::class, 'show_teacher'])->name('show_teacher');
Route::post('edit_teacher', [TeacherController::class, 'edit_teacher'])->name('edit_teacher');
Route::post('update_teacher', [TeacherController::class, 'update_teacher'])->name('update_teacher');
Route::post('delete_teacher', [TeacherController::class, 'delete_teacher'])->name('delete_teacher');
Route::post('delete_teacher_course', [TeacherController::class, 'delete_teacher_course'])->name('delete_teacher_course');
Route::get('teacher_profile/{id}', [TeacherController::class, 'teacher_profile'])->name('teacher_profile');
Route::get('show_teacher_courses', [TeacherController::class, 'show_teacher_courses'])->name('show_teacher_courses');


//course

Route::match(['get', 'post'], 'course', [CourseController::class, 'index'])->name('course');
Route::post('add_course', [CourseController::class, 'add_course'])->name('add_course');
Route::get('show_course', [CourseController::class, 'show_course'])->name('show_course');
Route::post('edit_course', [CourseController::class, 'edit_course'])->name('edit_course');
Route::post('update_course', [CourseController::class, 'update_course'])->name('update_course');
Route::post('delete_course', [CourseController::class, 'delete_course'])->name('delete_course');
Route::get('course_profile/{id}', [CourseController::class, 'course_profile'])->name('course_profile');
Route::post('course_profile_data', [CourseController::class, 'course_profile_data'])->name('course_profile_data');
Route::get('show_enroll_student', [CourseController::class, 'show_enroll_student'])->name('show_enroll_student');
Route::post('delete_new', [CourseController::class, 'delete_new'])->name('delete_new');


//offer

Route::match(['get', 'post'], 'offer', [OfferController::class, 'index'])->name('offer');
Route::post('add_offer', [OfferController::class, 'add_offer'])->name('add_offer');
Route::get('show_offer', [OfferController::class, 'show_offer'])->name('show_offer');
Route::post('edit_offer', [OfferController::class, 'edit_offer'])->name('edit_offer');
Route::post('update_offer', [OfferController::class, 'update_offer'])->name('update_offer');
Route::post('delete_offer', [OfferController::class, 'delete_offer'])->name('delete_offer');
Route::get('offer_profile/{id}', [OfferController::class, 'offer_profile'])->name('offer_profile');
Route::post('offer_profile_data', [OfferController::class, 'offer_profile_data'])->name('offer_profile_data');
Route::get('/get_course/{id}', [OfferController::class, 'get_course']);


//enrollement

Route::match(['get', 'post'], 'enrol', [EnrolController::class, 'index'])->name('enrol');
Route::match(['get', 'post'], 'student_details/{id}', [EnrolController::class, 'student_details'])->name('student_details');
Route::match(['get', 'post'], 'course_details/{id}', [EnrolController::class, 'course_details'])->name('course_details');
Route::match(['get', 'post'], 'all_students/{id}', [EnrolController::class, 'all_students'])->name('all_students');
Route::match(['get', 'post'], 'current_offers', [EnrolController::class, 'current_offers'])->name('current_offers');
Route::post('add_enroll', [EnrolController::class, 'add_enroll'])->name('add_enroll');
Route::get('show_enroll', [EnrolController::class, 'show_enroll'])->name('show_enroll');
Route::post('edit_enroll', [EnrolController::class, 'edit_enroll'])->name('edit_enroll');
Route::post('delete_enroll', [EnrolController::class, 'delete_enroll'])->name('delete_enroll');
Route::post('update_enroll', [EnrolController::class, 'update_enroll'])->name('update_enroll');
Route::post('add_student2', [EnrolController::class, 'add_student2'])->name('add_student2');



//Report

Route::match(['get', 'post'], 'income_report', [ReportController::class, 'income_report'])->name('income_report');
Route::match(['get', 'post'], 'course_income_report', [ReportController::class, 'course_income_report'])->name('course_income_report');
Route::match(['get', 'post'], 'all_courses_income', [ReportController::class, 'all_courses_income'])->name('all_courses_income');



//winloss

Route::match(['get', 'post'], 'winlos', [WinnLosController::class, 'index'])->name('winlos');
Route::post('add_winlos', [WinnLosController::class, 'add_winlos'])->name('add_winlos');
Route::get('show_winlos', [WinnLosController::class, 'show_winlos'])->name('show_winlos');
Route::post('edit_winlos', [WinnLosController::class, 'edit_winlos'])->name('edit_winlos');
Route::post('update_winlos', [WinnLosController::class, 'update_winlos'])->name('update_winlos');
Route::post('delete_winlos', [WinnLosController::class, 'delete_winlos'])->name('delete_winlos');
