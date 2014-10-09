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
Route::pattern('page', '[0-9]+');
Route::when('*', 'csrf', array('post', 'put', 'delete', 'update'));

Route::get('/signin', array('as' => 'signIn', 'uses' => 'InstruktorController@signIn'));
Route::get('/login', array('as' => 'signIn2', 'uses' => 'InstruktorController@signIn'));
Route::get('/', array('as' => 'signIn3', 'uses' => 'InstruktorController@signIn'));
Route::post('/login', array('as' => 'login', 'uses' => 'InstruktorController@login'));
Route::get('/logout', array('as' => 'logout', 'uses' => 'InstruktorController@logout'));

Route::group(array('before' => 'auth'), function(){
Route::post('/Klijent/Suggestions', array('uses' => 'KlijentController@getSuggestionedKlijents', 'as' => 'Klijent.Suggestions'));

//search
Route::post('/Instruktor/list/{page}/{searchString?}', array('as' => 'Instruktor.list', 'uses' => 'InstruktorController@_list'));
Route::post('/Ucionica/list/{page}/{searchString?}', array('as' => 'Ucionica.list', 'uses' => 'UcionicaController@_list'));
Route::post('/Klijent/list/{page}/{searchString?}', array('as' => 'Klijent.list', 'uses' => 'KlijentController@_list'));


Route::resource('Ucionica', 'UcionicaController');
Route::resource('Rezervacija', 'RezervacijaController', array('except' => array('index', 'update', 'edit')));
Route::resource('Instruktor', 'InstruktorController');
Route::resource('Klijent', 'KlijentController', array('except' => array('destroy')));
Route::resource('Kategorija', 'KategorijaController', array('except' => array('create', 'edit')));
Route::resource('Predmet', 'PredmetController', array('except' => array('index', 'create')));
Route::get('/Predmet/create/{kategorija_id}', array('as' => 'Predmet.create', 'uses' => 'PredmetController@create'));

Route::post('/Predmet/{id}/enable', array('as' => 'Predmet.enable', 'uses' => 'PredmetController@enable'));
Route::post('/Predmet/{id}/disable', array('as' => 'Predmet.disable', 'uses' => 'PredmetController@disable'));
Route::post('/Kategorija/{id}/enable', array('as' => 'Kategorija.enable', 'uses' => 'KategorijaController@enable'));
Route::post('/Kategorija/{id}/disable', array('as' => 'Kategorija.disable', 'uses' => 'KategorijaController@disable'));
Route::get('/Kategorija/{id}/Children', array('as' => 'Kategorija.children', 'uses' => 'KategorijaController@getChildren'));

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