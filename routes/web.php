<?php

use Illuminate\Support\Facades\Route;

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

//Route::get('/', function () {
  //  return view('welcome');
//});


Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/new-appointment/{doctorId}/{date}','App\Http\Controllers\FrontendController@show')->name('create.appointment');


Route::group(['middleware'=>['auth','admin']],function(){


	Route::resource('doctor','App\Http\Controllers\DoctorController');
	Route::get('/patients','App\Http\Controllers\PatientlistController@index')->name('patient');
//	Route::get('/patients/all','PatientlistController@allTimeAppointment')->name('all.appointments');
   Route::get('/status/update/{id}','App\Http\Controllers\PatientlistController@toggleStatus')->name('update.status');
	 Route::resource('department','App\Http\Controllers\DepartmentController');

});


Route::group(['middleware'=>['auth','patient']],function(){


Route::get('/user-profile','App\Http\Controllers\ProfileController@index');
Route::post('/user-profile','App\Http\Controllers\ProfileController@store')->name('profile.store');
Route::post('/profile-pic','App\Http\Controllers\ProfileController@profilePic')->name('profile.pic');
Route::get('/my-prescription','App\Http\Controllers\FrontendController@myPrescription')->name('my.prescription');
Route::post('/book/appointment','App\Http\Controllers\FrontendController@store')->name('book.appointment')->middleware();

Route::get('bookings','App\Http\Controllers\FrontendController@myBookings')->name('booking.index')->middleware();


});

Route::group(['middleware'=>['auth','doctor']], function(){

Route::resource('appointment','App\Http\Controllers\AppointmentController');

Route::post('/appointment/check','App\Http\Controllers\AppointmentController@check')->name('appointment.check');

Route::post('/appointment/update','App\Http\Controllers\AppointmentController@updateTime')->name('update');

Route::get('/patient-today','App\Http\Controllers\PrescriptionController@index')->name('patients.today');

Route::post('/prescription','App\Http\Controllers\PrescriptionController@store');

Route::get('/prescription/{userId}/{date}','App\Http\Controllers\PrescriptionController@show')->name('prescription.show');

Route::get('/prescribed-patients','App\Http\Controllers\PrescriptionController@patientsFromPrescription')->name('prescribed.patients');

});







Route::get('/','App\Http\Controllers\FrontendController@index');




Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

