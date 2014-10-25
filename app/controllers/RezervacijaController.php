<?php

class RezervacijaController extends \BaseController {

	public function __construct()
    {
    	$this->beforeFilter('myRezervacija', array('except' =>
    		array('create', 'store')));
    }

	private function itemNotFound(){
		Session::flash(BaseController::DANGER_MESSAGE_KEY, Rezervacija::NOT_FOUND_MESSAGE);
		return Redirect::route('Instruktor.show', Auth::id());
	}

	public function create_naplata($id){
		$naplata =  new Naplata();
		$naplata->rezervacija = Rezervacija::with('predmet', 'predmet.cijene', 'mjera', 'klijenti')->find($id);
		if(!$naplata->rezervacija)
			return $this->itemNotFound();
		if(!$naplata->rezervacija->predmet)
			return Redirect::route('Rezervacija.show', $id)
			->with(BaseController::DANGER_MESSAGE_KEY, 'Predmet rezervacije se više ne nalazi u sustavu. Nije moguća naplata.');
		if(strtotime($naplata->rezervacija->pocetak_rada) > time())
		{
			Session::flash(self::DANGER_MESSAGE_KEY, 'Nije moguće naplatiti instrukcije prije nego se odrade.');
			return Redirect::route('Rezervacija.show', $id);
		}

		$naplata->stvarnaMjera = $naplata->rezervacija->mjera;
		$naplata->stvarna_kolicina = $naplata->rezervacija->kolicina;
		return View::make('Rezervacija.Naplata.create')
		->with('naplata', $naplata);
	}

	public function store_naplata($id){
		$naplata = new Naplata();
		$rezervacija = Rezervacija::with('predmet', 'klijenti')
		->find($id);
		if($rezervacija == null)
			return $this->itemNotFound();

		if(strtotime($rezervacija->pocetak_rada) > time())
		{
			Session::flash(self::DANGER_MESSAGE_KEY, 'Nije moguće naplatiti instrukcije prije nego se odrade.');
			return Redirect::route('Rezervacija.show', $id);
		}

		$naplata->rezervacija_id = $rezervacija->id;
		$naplata->stvarna_kolicina = $rezervacija->kolicina;
		$naplata->stvarna_mjera = $rezervacija->mjera_id;
		if(Input::get('mjerechanged') == 'yes'){
			$stvarna_kolicina = Input::get('stvarna_kolicina');
			if($stvarna_kolicina){
				if($stvarna_kolicina < 1)
				{
					Session::flash(BaseController::DANGER_MESSAGE_KEY, 'Količina ne može biti manja od 1.');
					return Redirect::route('Test', $rezervacija->id)
					->withInput();
				}
				$naplata->stvarna_kolicina = $stvarna_kolicina;
			}
			$stvarna_mjera = Input::get('stvarna_mjera');
			if($stvarna_mjera)
			{
				$mjera = Mjera::find($stvarna_mjera);
				if(!$mjera){
					Session::flash(BaseController::DANGER_MESSAGE_KEY, Mjera::NOT_FOUND_MESSAGE);
					return Redirect::route('Test', $rezervacija->id)
					->withInput();
				}
				$naplata->stvarna_mjera = $stvarna_mjera;
			}
		}
		//
		$polaznici = $rezervacija->klijenti;
		$changes = array();
		$missed_count = 0;
		foreach ($polaznici as $polaznik) {
			if(Input::has('polaznicichanged') && Input::has('klijent-came-'.$polaznik->broj_mobitela)){
				$changes[$polaznik->broj_mobitela] = array('missed' => 1);
				$missed_count++;
			}
			else
				$changes[$polaznik->broj_mobitela] = array('missed' => 0);
		}
		//obracun iznosa
		$broj_polaznika = $polaznici->count() - $missed_count;
		$cijena = $rezervacija->predmet
		->cijene()
		->where('id', '=', $naplata->stvarna_mjera)
		->first()->pivot;

		$ukupno_satnica_po_polazniku = $cijena->individualno - $cijena->popust * ($broj_polaznika - 1);
		if($ukupno_satnica_po_polazniku < $cijena->minimalno)
			$ukupno_satnica_po_polazniku = $cijena->minimalno;
		$satnica_instruktora = $naplata->getSatnicaZaInstruktora($ukupno_satnica_po_polazniku) * $broj_polaznika;
		$ukupno_satnica = $ukupno_satnica_po_polazniku*$broj_polaznika;

		$naplata->ukupno_uplaceno = $ukupno_satnica * $naplata->stvarna_kolicina;
		$naplata->za_instruktora = $satnica_instruktora * $naplata->stvarna_kolicina;
		$naplata->za_tvrtku = $naplata->ukupno_uplaceno - $naplata->za_instruktora;

		$naplata->save();
		if(count($changes) > 0)
			$rezervacija->klijenti()->sync($changes);
		Session::flush(BaseController::SUCCESS_MESSAGE_KEY, 'Uspješno ste naplatili');
		return Redirect::route('Rezervacija.show', $id);
	}

	public function destroy_naplata($id){
		if(!Auth::user()->is_admin)
			return Redirect::route('Rezervacija.show', $id)
		->with(BaseController::DANGER_MESSAGE_KEY, 'Nemate pravo pristupa.');
		$naplata = Naplata::find($id)->delete();
		$rezervacija = Rezervacija::with('klijenti')->find($id);
		$updates = array();
		foreach ($rezervacija->klijenti as $klijent) {
			$updates[$klijent->broj_mobitela] = array('missed' => 0);
		}
		$rezervacija->klijenti()->sync($updates);
		return Redirect::route('Rezervacija.show', $id)
		->with(BaseController::SUCCESS_MESSAGE_KEY, 'Naplata je uspješno poništena');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('Rezervacija.create')
		->with('klijent', View::make('Klijent.listForm'))
		->with('predmet', View::make('Kategorija.select'));
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
			return Redirect::route('Rezervacija.create')
			->withInput();
		}

		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Rezervacija je uspješno dodana.');
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

		return View::make('Rezervacija.create')
		->with('klijent', View::make('Klijent.listForm')
			->with('klijenti', $rezervacija->klijenti))
		->with('predmet', View::make('Kategorija.select')
			->with('predmet_id', $rezervacija->predmet_id));
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

		return View::make('Rezervacija.create')
		->with('rezervacija', $rezervacija)
		->with('klijent', View::make('Klijent.listForm')
			->with('klijenti', $rezervacija->klijenti))
		->with('predmet', View::make('Kategorija.select')
			->with('predmet_id', $rezervacija->predmet_id));;
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

		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Rezervacija je uspješno promijenjena.');
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
		return View::make('Rezervacija.show')
		->with('rezervacija', $rezervacija);
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

}