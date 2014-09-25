<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
Route::pattern('id', '[0-9]+');
Route::pattern('tjedan', '[0-9]+');
Route::pattern('godina', '[0-9]+');

Route::get('/signin', array('as' => 'signIn', 'uses' => 'InstruktorController@signIn'));
Route::get('/login', array('as' => 'signIn2', 'uses' => 'InstruktorController@signIn'));
Route::get('/', array('as' => 'signIn3', 'uses' => 'InstruktorController@signIn'));
Route::post('/login', array('as' => 'login', 'uses' => 'InstruktorController@login'));
Route::get('/logout', array('as' => 'logout', 'uses' => 'InstruktorController@logout'));

Route::group(array('before' => 'auth'), function(){

//search
Route::get('/Instruktor/search', array('as' => 'Instruktor.search', 'uses' => 'InstruktorController@index'));
Route::get('/Ucionica/search', array('as' => 'Ucionica.search', 'uses' => 'UcionicaController@index'));
Route::get('/Klijent/search', array('as' => 'Klijent.search', 'uses' => 'KlijentController@index'));


Route::resource('Ucionica', 'UcionicaController');
Route::resource('Rezervacija', 'RezervacijaController', array('except' => array('index', 'update', 'edit')));
Route::resource('Instruktor', 'InstruktorController');
Route::resource('Klijent', 'KlijentController', array('except' => array('destroy')));

Route::get('/Instruktor/{id}/changePassword', array('as' => 'Instruktor.changePassword', 'uses' => 'InstruktorController@changePassword'));
Route::post('/Instruktor/{id}/changePassword', array('as' => 'Instruktor.postChangePassword', 'uses' => 'InstruktorController@postChangePassword'));

Route::get('/Ucionica/{id}/{tjedan?}/{godina?}', array('uses' => 'UcionicaController@show', 'as' => 'Ucionica.raspored'));
Route::get('/Instruktor/{id}/{tjedan?}/{godina?}', array('uses' => 'InstruktorController@show', 'as' => 'Instruktor.raspored'));

Route::get('/Rezervacija/{id}/naplati', array('uses' => 'RezervacijaController@naplati', 'as' => 'Rezervacija.naplati'));
Route::put('/Rezervacija/{id}/naplata', array('uses' => 'RezervacijaController@naplata', 'as' => 'Rezervacija.naplata'));
Route::delete('/Rezervacija/{id}/destroy_naplata', array('uses' => 'RezervacijaController@destroy_naplata', 'as' => 'Rezervacija.destroy_naplata'));

Route::get('/Izvjestaj/{id}/Godina/{godina?}', array('uses' => 'IzvjestajController@godisnji_izvjestaj', 'as' => 'Izvjestaj.godisnji'));
Route::get('/Izvjestaj/{id}/Tjedan/{tjedan?}/{godina?}', array('uses' => 'IzvjestajController@tjedni_izvjestaj', 'as' => 'Izvjestaj.tjedni'));
Route::get('/Izvjestaj/Godina/{godina?}', array('uses' => 'IzvjestajController@ukupni_godisnji_izvjestaj', 'as' => 'Izvjestaj.ukupni_godisnji'));
Route::get('/Izvjestaj/Tjedan/{tjedan?}/{godina?}', array('uses' => 'IzvjestajController@ukupni_tjedni_izvjestaj', 'as' => 'Izvjestaj.ukupni_tjedni'));

});