<?php

class RezervacijaController extends \BaseController {

	protected $layout = 'layouts.master';

	public function __construct()
    {
    	$this->beforeFilter('myRezervacija', array('except' =>
    		array('create', 'store')));
    }

	private function itemNotFound(){
		Session::flash(BaseController::DANGER_MESSAGE_KEY, Rezervacija::NOT_FOUND_MESSAGE);
		return Redirect::route('Instruktor.show', Auth::id());
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$this->layout->title = "Nova rezervacija";
		$rows = Ucionica::select('id', 'naziv', 'max_broj_ucenika')->get();
		foreach ($rows as $row) {
			$ucionice[$row->id] = $row->naziv.'('.$row->max_broj_ucenika.')';
		}

		$this->layout->content =
		$v = View::make('Rezervacija.create')
		->with('ucionice', $ucionice)
		->with('klijent', View::make('Klijent.listForm'))
		->with('predmet', View::make('Kategorija.select'));
		return $this->layout;
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::all();
		$input['instruktor_id'] = Auth::id();
		$rezervacija = new Rezervacija();

		//provjera ispravnosti podataka
		$errorMessage = $rezervacija->getErrorOrSync($input);
		if($errorMessage != null){
			Session::flash(self::DANGER_MESSAGE_KEY, $errorMessage);
			return Redirect::route('Rezervacija.create', $id)
			->withInput();
		}

		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Rezervacia je uspješno dodana.');
		return Redirect::route('Rezervacija.show', array('id' => $rezervacija->id));
	}

	/**
	 * Show the form for editing a copy of the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function copy($id)
	{
		$rezervacija = Rezervacija::with('klijenti')->find($id);
		//provjera postojanja
		if(!$rezervacija)
			return $this->itemNotFound();

		$rows = Ucionica::select('id', 'naziv', 'max_broj_ucenika')->get();
		foreach ($rows as $row) {
			$ucionice[$row->id] = $row->naziv.'('.$row->max_broj_ucenika.')';
		}

		if(!$rezervacija->predmet)
			$this->layout->title = 'Nepoznato';
		else $this->layout->title = $rezervacija->predmet->ime;
		$this->layout->title .= " - Uredi rezervaciju";
		$this->layout->content =
		View::make('Rezervacija.create')
		->with('ucionice', $ucionice)
		->with('klijent', View::make('Klijent.listForm')
			->with('klijenti', $rezervacija->klijenti))
		->with('predmet', View::make('Kategorija.select')
			->with('predmet_id', $rezervacija->predmet_id));;
		return $this->layout;
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$rezervacija = Rezervacija::with('klijenti', 'mjera', 'ucionica')->find($id);
		//provjera postojanja
		if(!$rezervacija)
			return $this->itemNotFound();

		//provjera dozvole
		if(strtotime($rezervacija->pocetak_rada) < time() && !Auth::user()->is_admin) {
			Session::flash(self::DANGER_MESSAGE_KEY, 'Samo je administratoru dozvoljeno uređivati započetu rezervaciju.');
			return Redirect::route('Rezervacija.show', $id);
		}

		$rows = Ucionica::select('id', 'naziv', 'max_broj_ucenika')->get();
		foreach ($rows as $row) {
			$ucionice[$row->id] = $row->naziv.'('.$row->max_broj_ucenika.')';
		}

		if(!$rezervacija->predmet)
			$this->layout->title = 'Nepoznato';
		else $this->layout->title = $rezervacija->predmet->ime;
		$this->layout->title .= " - Uredi rezervaciju";
		$this->layout->content =
		View::make('Rezervacija.create')
		->with('rezervacija', $rezervacija)
		->with('ucionice', $ucionice)
		->with('klijent', View::make('Klijent.listForm')
			->with('klijenti', $rezervacija->klijenti))
		->with('predmet', View::make('Kategorija.select')
			->with('predmet_id', $rezervacija->predmet_id));;
		return $this->layout;
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::all();
		$input['instruktor_id'] = Auth::id();
		$rezervacija = Rezervacija::find($id);

		//provjera postojanja
		if(!$rezervacija)
			return $this->itemNotFound();

		//provjera ispravnosti podataka
		$errorMessage = $rezervacija->getErrorOrSync($input);
		if($errorMessage != null){
			Session::flash(self::DANGER_MESSAGE_KEY, $errorMessage);
			return Redirect::route('Rezervacija.edit', $id)
			->withInput();
		}

		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Rezervacia je uspješno promijenjena.');
		return Redirect::route('Rezervacija.show', array('id' => $rezervacija->id));
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$rezervacija = Rezervacija::with('naplata', 'predmet', 'mjera', 'klijenti')->find($id);
		if(!$rezervacija)
			return $this->itemNotFound();
		$this->layout->title = "Prikaz rezervacije";
		$this->layout->content =
		View::make('Rezervacija.show')
		->with('rezervacija', $rezervacija);
		return $this->layout;
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$rezervacija = Rezervacija::find($id);
		if(!$rezervacija)
			return $this->itemNotFound();
		if(strtotime($rezervacija->pocetak_rada) < time() && !Auth::user()->is_admin) {
			Session::flash(self::DANGER_MESSAGE_KEY, 'Samo je administratoru dozvoljeno ukloniti započetu rezervaciju.');
			return Redirect::route('Rezervacija.show', $id);
		}
		$rezervacija->delete();
		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Rezervacija je oslobođena.');
		return Redirect::route('Instruktor.show', Auth::id());
	}

	public function naplati($id)
	{
		$r = Rezervacija::find($id);
		if(strtotime($r->pocetak_rada) > time())
		{
			Session::flash(self::DANGER_MESSAGE_KEY, 'Nije moguće naplatiti instrukcije prije nego se odrade.');
			return Redirect::route('Rezervacija.show', $id);
		}
		$this->layout->title = "Naplata";
		$v = View::make('Rezervacija.naplati')
		->with('rezervacija', $r);
		if(!is_null($r->naplata))
			$v->with('naplata', $r->naplata);
		$this->layout->content = $v;
		return $this->layout;
	}

	public function naplata($id)
	{
		$r = Rezervacija::find($id);
		$n = $r->naplata;
		if(strtotime($r->pocetak_rada) > time())
		{
			Session::flash(self::DANGER_MESSAGE_KEY, 'Nije moguće naplatiti instrukcije prije nego se odrade.');
			return Redirect::route('Rezervacija.show', $id);
		}

		if(!(Input::has('za_tvrtku')&& Input::has('ukupno_uplaceno')&&Input::get('za_instruktora')))
		{
			Session::flash(self::DANGER_MESSAGE_KEY, 'Niste unijeli sve potrebne podatke.');
			return Redirect::route('Rezervacija.naplati', $id)
			->withInput();
		}


		if(Input::get('za_tvrtku') < 0 || Input::get('za_instruktora') < 0)
		{
			Session::flash(self::DANGER_MESSAGE_KEY, 'Nepravilan unos. Iznosi en mogu biti negativni.');
			return Redirect::route('Rezervacija.naplati', $id)
			->withInput();
		}

		if(Input::get('za_tvrtku') + Input::get('za_instruktora') != Input::get('ukupno_uplaceno'))
		{
			Session::flash(self::DANGER_MESSAGE_KEY, 'Nepravilan unos. Ukupni iznos se dijeli na tvrtku i instruktora.');
			return Redirect::route('Rezervacija.naplati', $id)
			->withInput();
		}

		if(is_null($n))
		{
			$n = Naplata::create(Input::all());
			$n->save();
		}
		else $n->update(Input::all());
		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Naplata je uspješna.');
		return Redirect::route('Rezervacija.show', $id);
	}

	public function destroy_naplata($id)
	{
		Rezervacija::find($id)->naplata->delete();
		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Naplata je uspješno uklonjena');
		return Redirect::route('Rezervacija.show', $id);
	}


}