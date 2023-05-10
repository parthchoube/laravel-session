<?php
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    if(!Auth::check()) 
    {
        return redirect('admin/login');
    }else{
        return redirect()->intended('admin/dashboard');
    }
});
/*Route::get('/dashboard', function () {
        return view('admin/dashboard');
    });*/
Route::get('login', 'AuthController@login')->name('admin.login');
Route::post('login', 'AuthController@doLogin')->name('admin.doLogin');
Route::group(['middleware' => 'auth'], function () {
	Route::get('dashboard', 'DashboardController@index')->name('admin.dashboard');
	Route::get('logout', 'AuthController@logout')->name('admin.logout');

    Route::get('/users','AdminController@profile')->name('admin.users'); 
    Route::get('/user/destroy/{id}','AdminController@destroy')->name('admin.destroy');
    Route::get('/user/add','AdminController@addUser')->name('admin.users.add');
    Route::get('/user/edit/{id}', 'AdminController@addUser')->name('admin.users.edit');
    Route::post('/user/store','AdminController@store')->name('admin.storeUser');
    Route::post('/user/update/{id}','AdminController@update')->name('admin.updateUser');
    Route::get('/user/view/{id}', 'AdminController@view')->name('admin.users.view');
    Route::get('profile','AdminController@Myprofile')->name('admin.Myprofile');
});