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
Route::pattern('Djelatnik', '[0-9]+');
Route::pattern('Ucionica', '[0-9]+');
Route::pattern('Klijent', '[0-9]+');
Route::pattern('Rezervacija', '[0-9]+');
Route::pattern('Role', '[0-9]+');
Route::pattern('Cjenovnik', '[0-9]+');
Route::pattern('Predmet', '[0-9]+');
Route::pattern('Kategorija', '[0-9]+');
Route::pattern('NeradniDan', '[0-9]+');
Route::when('*', 'csrf', array('post', 'put', 'delete', 'update'));

Route::get('/signin', array('as' => 'signIn', 'uses' => 'DjelatnikController@signIn'));
Route::get('/login', array('uses' => 'DjelatnikController@signIn'));
Route::post('/login', array('as' => 'login', 'uses' => 'DjelatnikController@login'));
Route::get('/logout', array('as' => 'logout', 'uses' => 'DjelatnikController@logout'));
Route::get('/', function() {
    if (Auth::check()) {
        return Redirect::route('home');
    } else {
        return Redirect::route('signIn');
    }
});

Route::group(array('before' => 'auth'), function() {
    Route::post('/Klijent/Suggestions', array('uses' => 'KlijentController@getSuggestionedKlijents', 'as' => 'Klijent.Suggestions'));

//search
    Route::post('/Djelatnik/list/{page}/{searchString?}', array('as' => 'Djelatnik.list', 'uses' => 'DjelatnikController@_list'));
    Route::post('/Ucionica/list/{page}/{searchString?}', array('as' => 'Ucionica.list', 'uses' => 'UcionicaController@_list'));
    Route::post('/NeradniDan/list/{page}/{searchString?}', array('as' => 'NeradniDan.list', 'uses' => 'NeradniDanController@_list'));
    Route::post('/Klijent/list/{page}/{searchString?}', array('as' => 'Klijent.list', 'uses' => 'KlijentController@_list'));
    Route::post('/Role/list/{page}/{searchString?}', array('as' => 'Role.list', 'uses' => 'RoleController@_list'));
    Route::post('/Cjenovnik/list/{page}/{searchString?}', array('as' => 'Cjenovnik.list', 'uses' => 'CjenovnikController@_list'));
    
//cjenovnik table
    Route::post('/Cjenovnik/table/{Cjenovnik}', array('as' => 'Cjenovnik.table', 'uses' => 'CjenovnikController@_table'));

//restfull
    Route::resource('Djelatnik', 'DjelatnikController');
    Route::resource('Ucionica', 'UcionicaController');
    Route::resource('NeradniDan', 'NeradniDanController', array('except' => array('show')));
    Route::resource('Role', 'RoleController');
    Route::resource('Cjenovnik', 'CjenovnikController');
    Route::resource('Klijent', 'KlijentController', array('except' => array('destroy')));
    Route::get('/Klijent/{Klijent}/Rezervacije/{User}/{Predmet}', array('uses' => 'KlijentController@rezervacije', 'as' => 'Klijent.rezervacije'));
    Route::resource('Kategorija', 'KategorijaController', array('except' => array('create')));
    Route::resource('Predmet', 'PredmetController', array('except' => array('index', 'create')));
    Route::get('/Predmet/create/{Kategorija}', array('as' => 'Predmet.create', 'uses' => 'PredmetController@create'));
    Route::resource('Rezervacija', 'RezervacijaController', array('except' => array('index', 'create')));
    Route::get('/Rezervacija/create/{Djelatnik?}', array('uses' => 'RezervacijaController@create', 'as' => 'Rezervacija.create'));
    Route::get('/Rezervacija/{Rezervacija}/copy', array('as' => 'Rezervacija.copy', 'uses' => 'RezervacijaController@copy'));
    Route::get('/Rezervacija/{Rezervacija}/Naplata', array('uses' => 'RezervacijaController@create_naplata', 'as' => 'Naplata.create'));
    Route::put('/Rezervacija/{Rezervacija}/Naplata', array('uses' => 'RezervacijaController@store_naplata', 'as' => 'Naplata.store'));
    Route::delete('/Rezervacija/{id}/Naplata', array('uses' => 'RezervacijaController@destroy_naplata', 'as' => 'Naplata.destroy'));

//Excel
    Route::get('/Excel', array('uses' => 'ExcelController@index', 'as' => 'Excel.index'));
    Route::post('/Excel/download', array('uses' => 'ExcelController@download', 'as' => 'Excel.download'));

    Route::get('/Kategorija/{Djelatnik}/Children/{id?}', array('as' => 'Kategorija.children', 'uses' => 'KategorijaController@getChildren'));

    Route::get('/Djelatnik/{Djelatnik}/changePassword', array('as' => 'Djelatnik.changePassword', 'uses' => 'DjelatnikController@changePassword'));
    Route::post('/Djelatnik/{Djelatnik}/changePassword', array('as' => 'Djelatnik.postChangePassword', 'uses' => 'DjelatnikController@postChangePassword'));

//izvjestaji
    Route::get('/Izvjestaj/{id}/Godina/{godina?}', array('uses' => 'IzvjestajController@godisnji_izvjestaj', 'as' => 'Izvjestaj.godisnji'));
    Route::get('/Izvjestaj/{id}/Tjedan/{tjedan?}/{godina?}', array('uses' => 'IzvjestajController@tjedni_izvjestaj', 'as' => 'Izvjestaj.tjedni'));
    Route::get('/Izvjestaj/Godina/{godina?}', array('uses' => 'IzvjestajController@ukupni_godisnji_izvjestaj', 'as' => 'Izvjestaj.ukupni_godisnji'));
    Route::get('/Izvjestaj/Tjedan/{tjedan?}/{godina?}', array('uses' => 'IzvjestajController@ukupni_tjedni_izvjestaj', 'as' => 'Izvjestaj.ukupni_tjedni'));

//pocetna
    Route::get('/home', array('as' => 'home', function() {
            $t = new DateTime();
            $day = $t->format('N');
            $week = $t->format('W');
            $year = $t->format('o');
            return View::make('home')->with('day', $day)->with('week', $week)->with('year', $year);
        }));
 //listanje rasporeda
    Route::get('/home/{day}/{week}/{year}', array('as' => 'home.raspored', function($day, $week, $year) {
            $t = new DateTime();
            $t->setISODate($year, $week, $day);
            $day = $t->format('N');
            $week = $t->format('W');
            $year = $t->format('o');
            return View::make('home')->with('day', $day)->with('week', $week)->with('year', $year);
        }));
    Route::get('/Ucionica/{id}/{tjedan?}/{godina?}', array('uses' => 'UcionicaController@show', 'as' => 'Ucionica.raspored'));
    Route::get('/Djelatnik/{id}/{tjedan?}/{godina?}', array('uses' => 'DjelatnikController@show', 'as' => 'Djelatnik.raspored'));
//ajax listanje rasporeda
    Route::get('/home-raspored/{day}/{week}/{year}', function($day, $week, $year) {
        $t = new DateTime();
        $t->setISODate($year, $week, $day);
        $day = $t->format('N');
        $week = $t->format('W');
        $year = $t->format('o');
        return Helpers\Raspored::RasporedForDay($day, $week, $year);
    });
    Route::get('/Djelatnik-raspored/{Djelatnik}/{week}/{year}', function($Djelatnik, $week, $year) {
        $t = new DateTime();
        $t->setISODate($year, $week);
        $week = $t->format('W');
        $year = $t->format('o');
        return Helpers\Raspored::RasporedForUserInWeek($Djelatnik, $week, $year);
    });
    Route::get('/Ucionica-raspored/{Ucionica}/{week}/{year}', function($Ucionica, $week, $year) {
        $t = new DateTime();
        $t->setISODate($year, $week);
        $week = $t->format('W');
        $year = $t->format('o');
        return Helpers\Raspored::RasporedForUcionicaInWeek($Ucionica, $week, $year);
    });
});
