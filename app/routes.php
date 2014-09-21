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
Route::filter('admin', function(){
	if(!Auth::check()||!Auth::user()->is_admin)
	{
		Session::flash('poruka', 'Nemate pravo pristupiti zahtjevanom resursu.');
		return Redirect::to('logout')
		->with('poruka', 'Nemate pravo pristupiti zahtjevanom resursu.');
	}
});
Route::filter('myProfile', function($route){
	$id = $route->getParameter('id');
	if($id == null)
		$id = $route->getParameter('Instruktor');
	if(!Auth::check()||!(Auth::user()->is_admin||Auth::id()==$id))
	{
		Session::flash('poruka', 'Nemate pravo pristupiti zahtjevanom resursu.');
		return Redirect::to('logout');
	}
});
Route::filter('myRezervacija', function($route){
	$id = $route->getParameter('id');
	if($id == null)
		$id = $route->getParameter('Rezervacija');
	if(!Auth::check()||!(Auth::user()->is_admin||
		Auth::id()==Rezervacija::find($id)->instruktor_id))
	{
		Session::flash('poruka', 'Nemate pravo pristupiti zahtjevanom resursu.');
		return Redirect::to('logout');
	}
});

Route::get('/signin', array('as' => 'signIn', 'uses' => 'InstruktorController@signIn'));
Route::get('/login', array('as' => 'signIn2', 'uses' => 'InstruktorController@signIn'));
Route::get('/', array('as' => 'signIn3', 'uses' => 'InstruktorController@signIn'));
Route::post('/login', array('as' => 'login', 'uses' => 'InstruktorController@login'));
Route::get('/logout', array('as' => 'logout', 'uses' => 'InstruktorController@logout'));

Route::group(array('before' => 'auth'), function(){

Route::resource('Ucionica', 'UcionicaController');
Route::resource('Rezervacija', 'RezervacijaController', array('except' => array('index', 'update', 'edit')));
Route::resource('Instruktor', 'InstruktorController');

Route::get('/Instruktor/{id}/changePassword', array('as' => 'changePassword', 'uses' => 'InstruktorController@changePassword'));
Route::post('/Instruktor/{id}/changePassword', array('as' => 'pchangePassword', 'uses' => 'InstruktorController@postChangePassword'));

Route::get('/Ucionica/{id}/{tjedan?}/{godina?}', array('uses' => 'UcionicaController@show', 'as' => 'UcionicaController@showT'));
Route::get('/Instruktor/{id}/{tjedan?}/{godina?}', array('uses' => 'InstruktorController@show', 'as' => 'InstruktorController@showT'));

Route::get('/Rezervacija/{id}/naplati', array('uses' => 'RezervacijaController@naplati', 'as' => 'RezervacijaController@naplati'));
Route::put('/Rezervacija/{id}/naplata', array('uses' => 'RezervacijaController@naplata', 'as' => 'RezervacijaController@naplata'));
Route::delete('/Rezervacija/{id}/destroy_naplata', array('uses' => 'RezervacijaController@destroy_naplata', 'as' => 'RezervacijaController@destroy_naplata'));

Route::get('/Izvjestaj/{id}/Godina/{godina?}', array('uses' => 'IzvjestajController@godisnji_izvjestaj', 'as' => 'IzvjestajController@godisnji_izvjestaj'));
Route::get('/Izvjestaj/{id}/Tjedan/{tjedan?}/{godina?}', array('uses' => 'IzvjestajController@tjedni_izvjestaj', 'as' => 'IzvjestajController@tjedni_izvjestaj'));
Route::get('/Izvjestaj/Godina/{godina?}', array('uses' => 'IzvjestajController@ukupni_godisnji_izvjestaj', 'as' => 'IzvjestajController@ukupni_godisnji_izvjestaj'));
Route::get('/Izvjestaj/Tjedan/{tjedan?}/{godina?}', array('uses' => 'IzvjestajController@ukupni_tjedni_izvjestaj', 'as' => 'IzvjestajController@ukupni_tjedni_izvjestaj'));
});