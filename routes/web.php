<?php

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

Route::get('/login','AuthController@login')->name('login');
Route::post('/postlogin','AuthController@postlogin');
Route::get('/logout','AuthController@logout');

Route::group(['middleware' => ['auth','CheckRole:admin,petugas']], function () {

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/dashboard', 'DashboardController@index');

Route::get('/suratmasuk/index', 'SuratMasukController@index');
Route::get('/suratmasuk/create', 'SuratMasukController@create');
Route::post('/suratmasuk/store', 'SuratMasukController@store');
Route::get('/suratmasuk/{id}/show', 'SuratMasukController@show');
Route::get('/suratmasuk/{id}/edit', 'SuratMasukController@edit');
Route::post('/suratmasuk/{id}/update','SuratMasukController@update');
Route::get('/suratmasuk/{id}/destroy','SuratMasukController@destroy');
Route::get('/suratmasuk/agenda','SuratMasukController@agenda');
Route::get('/suratmasuk/agendamasukcetak_pdf','SuratMasukController@agendamasukcetak_pdf');
Route::get('/suratmasuk.agendamasukdownload_excel','SuratMasukController@agendamasukdownload_excel')->name('suratmasuk.downloadexcel');
Route::get('/suratmasuk/galeri','SuratMasukController@galeri');

Route::get('/suratkeluar/index', 'SuratKeluarController@index');
Route::get('/suratkeluar/create', 'SuratKeluarController@create');
Route::post('/suratkeluar/store', 'SuratKeluarController@store');
Route::get('/suratkeluar/{id}/show', 'SuratKeluarController@show');
Route::get('/suratkeluar/{id}/edit', 'SuratKeluarController@edit');
Route::post('/suratkeluar/{id}/update','SuratKeluarController@update');
Route::get('/suratkeluar/{id}/destroy','SuratKeluarController@destroy');
Route::get('/suratkeluar/agenda','SuratKeluarController@agenda');
Route::get('/suratkeluar/agendakeluarcetak_pdf','SuratKeluarController@agendakeluarcetak_pdf');
Route::get('/suratkeluar.agendakeluardownload_excel','SuratKeluarController@agendakeluardownload_excel')->name('suratkeluar.downloadexcel');
Route::get('/suratkeluar/galeri','SuratKeluarController@galeri');

Route::get('/klasifikasi', 'KlasifikasiController@index');
Route::get('/klasifikasi/index','KlasifikasiController@index');
Route::get('/klasifikasi/create','KlasifikasiController@create');
Route::post('/klasifikasi/store','KlasifikasiController@store');
Route::get('/klasifikasi/{id}/edit','KlasifikasiController@edit');
Route::post('/klasifikasi/{id}/update','KlasifikasiController@update');
Route::get('/klasifikasi/{id}/destroy','KlasifikasiController@destroy');

Route::get('disposisi/{suratmasuk}', 'DisposisiController@index')->name('disposisi.index');
Route::post('disposisi/{suratmasuk}', 'DisposisiController@store')->name('disposisi.store');
Route::get('disposisi/{suratmasuk}/create', 'DisposisiController@create')->name('disposisi.create');
Route::get('disposisi/{suratmasuk}/{id}/edit', 'DisposisiController@edit')->name('disposisi.edit');
Route::get('disposisi/{suratmasuk}/{id}', 'DisposisiController@update')->name('disposisi.update');
Route::delete('disposisi/{suratmasuk}/{id}', 'DisposisiController@destroy')->name('disposisi.destroy');
Route::get('/disposisi/{suratmasuk}/{id}/download', 'DisposisiController@download')->name('disposisi.download');
});

Route::group(['middleware' => ['auth','CheckRole:admin']], function () {
    Route::resource('instansi', 'InstansiController');
    Route::resource('/pengguna','PenggunaController');
});



