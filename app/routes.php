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
Route::pattern('kategorija_id', '[0-9]+');
Route::pattern('user_id', '[0-9]+');
Route::pattern('Instruktor', '[0-9]+');
Route::pattern('Ucionica', '[0-9]+');
Route::pattern('Klijent', '[0-9]+');
Route::pattern('Rezervacija', '[0-9]+');
Route::pattern('Role', '[0-9]+');
Route::pattern('Predmet', '[0-9]+');
Route::pattern('Kategorija', '[0-9]+');
Route::when('*', 'csrf', array('post', 'put', 'delete', 'update'));

Route::get('/signin', array('as' => 'signIn', 'uses' => 'InstruktorController@signIn'));
Route::post('/login', array('as' => 'login', 'uses' => 'InstruktorController@login'));
Route::get('/logout', array('as' => 'logout', 'uses' => 'InstruktorController@logout'));
Route::get('/', function() {
    if (Auth::check()) {
        return Reditect::route('home');
    } else {
        return Redirect::route('signIn');
    }
});

Route::group(array('before' => 'auth'), function() {
    Route::post('/Klijent/Suggestions', array('uses' => 'KlijentController@getSuggestionedKlijents', 'as' => 'Klijent.Suggestions'));

//search
    Route::post('/Instruktor/list/{page}/{searchString?}', array('as' => 'Instruktor.list', 'uses' => 'InstruktorController@_list'));
    Route::post('/Ucionica/list/{page}/{searchString?}', array('as' => 'Ucionica.list', 'uses' => 'UcionicaController@_list'));
    Route::post('/Klijent/list/{page}/{searchString?}', array('as' => 'Klijent.list', 'uses' => 'KlijentController@_list'));
    Route::post('/Role/list/{page}/{searchString?}', array('as' => 'Role.list', 'uses' => 'RoleController@_list'));

//restfull
    Route::resource('Instruktor', 'InstruktorController');
    Route::resource('Ucionica', 'UcionicaController');
    Route::resource('Role', 'RoleController');
    Route::resource('Klijent', 'KlijentController', array('except' => array('destroy')));
    Route::resource('Kategorija', 'KategorijaController', array('except' => array('create')));
    Route::resource('Predmet', 'PredmetController', array('except' => array('index', 'create')));
    Route::get('/Predmet/create/{kategorija_id}', array('as' => 'Predmet.create', 'uses' => 'PredmetController@create'));
    Route::resource('Rezervacija', 'RezervacijaController', array('except' => array('index')));
    Route::get('/Rezervacija/{id}/copy', array('as' => 'Rezervacija.copy', 'uses' => 'RezervacijaController@copy'));
    Route::get('/Rezervacija/{id}/Naplata', array('uses' => 'RezervacijaController@create_naplata', 'as' => 'Naplata.create'));
    Route::put('/Rezervacija/{id}/Naplata', array('uses' => 'RezervacijaController@store_naplata', 'as' => 'Naplata.store'));
    Route::delete('/Rezervacija/{id}/Naplata', array('uses' => 'RezervacijaController@destroy_naplata', 'as' => 'Naplata.destroy'));

//Excel
    Route::get('/Excel', array('uses' => 'ExcelController@index', 'as' => 'Excel.index'));
    Route::post('/Excel/download', array('uses' => 'ExcelController@download', 'as' => 'Excel.download'));

    Route::get('/Kategorija/{user_id}/Children/{id?}', array('as' => 'Kategorija.children', 'uses' => 'KategorijaController@getChildren'));

    Route::get('/Instruktor/{id}/changePassword', array('as' => 'Instruktor.changePassword', 'uses' => 'InstruktorController@changePassword'));
    Route::post('/Instruktor/{id}/changePassword', array('as' => 'Instruktor.postChangePassword', 'uses' => 'InstruktorController@postChangePassword'));

//izvjestaji
    Route::get('/Izvjestaj/{id}/Godina/{godina?}', array('uses' => 'IzvjestajController@godisnji_izvjestaj', 'as' => 'Izvjestaj.godisnji'));
    Route::get('/Izvjestaj/{id}/Tjedan/{tjedan?}/{godina?}', array('uses' => 'IzvjestajController@tjedni_izvjestaj', 'as' => 'Izvjestaj.tjedni'));
    Route::get('/Izvjestaj/Godina/{godina?}', array('uses' => 'IzvjestajController@ukupni_godisnji_izvjestaj', 'as' => 'Izvjestaj.ukupni_godisnji'));
    Route::get('/Izvjestaj/Tjedan/{tjedan?}/{godina?}', array('uses' => 'IzvjestajController@ukupni_tjedni_izvjestaj', 'as' => 'Izvjestaj.ukupni_tjedni'));

//pocetna
    Route::get('/home', array('as' => 'pocetna', function() {
            $t = new DateTime();
            $day = $t->format('N');
            $week = $t->format('W');
            $year = $t->format('o');
            return View::make('home')->with('day', $day)->with('week', $week)->with('year', $year);
        }));
 //listanje rasporeda
    Route::get('/home/{day}/{week}/{year}', array('as' => 'home', function($day, $week, $year) {
            $t = new DateTime();
            $t->setISODate($year, $week, $day);
            $day = $t->format('N');
            $week = $t->format('W');
            $year = $t->format('o');
            return View::make('home')->with('day', $day)->with('week', $week)->with('year', $year);
        }));
    Route::get('/Ucionica/{id}/{tjedan?}/{godina?}', array('uses' => 'UcionicaController@show', 'as' => 'Ucionica.raspored'));
    Route::get('/Instruktor/{id}/{tjedan?}/{godina?}', array('uses' => 'InstruktorController@show', 'as' => 'Instruktor.raspored'));
//ajax listanje rasporeda
    Route::get('/home-raspored/{day}/{week}/{year}', function($day, $week, $year) {
        $t = new DateTime();
        $t->setISODate($year, $week, $day);
        $day = $t->format('N');
        $week = $t->format('W');
        $year = $t->format('o');
        return Helpers\Raspored::RasporedForDay($day, $week, $year);
    });
    Route::get('/Instruktor-raspored/{user_id}/{week}/{year}', function($user_id, $week, $year) {
        $t = new DateTime();
        $t->setISODate($year, $week);
        $week = $t->format('W');
        $year = $t->format('o');
        return Helpers\Raspored::RasporedForUserInWeek($user_id, $week, $year);
    });
    Route::get('/Ucionica-raspored/{ucionica_id}/{week}/{year}', function($ucionica_id, $week, $year) {
        $t = new DateTime();
        $t->setISODate($year, $week);
        $week = $t->format('W');
        $year = $t->format('o');
        return Helpers\Raspored::RasporedForUcionicaInWeek($ucionica_id, $week, $year);
    });
});
