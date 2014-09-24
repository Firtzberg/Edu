<?php

class RezervacijaController extends \BaseController {

	protected $layout = 'layouts.master';

	public function __construct()
    {
    	$this->beforeFilter('myRezervacija', array('except' =>
    		array('create', 'store')));
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
		$rows = Mjera::select('id', 'simbol')->get();
		foreach ($rows as $row) {
			$mjere[$row->id] = $row->simbol;
		}
		$this->layout->content =
		$v = View::make('Rezervacija.create')
		->with('ucionice', $ucionice)
		->with('mjere', $mjere);
		return $this->layout;
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$r = new Rezervacija();

		//provjera broja učenika
		$r->broj_ucenika = Input::get('broj_ucenika');
		if($r->broj_ucenika < 1){
			Session::flash('poruka', 'Broj učenika mora biti barem 1.');
			return Redirect::route('Rezervacija.create')
			->withInput();
		}

		//provjera vremena početka
		$dto = new DateTime(Input::get('datum'));
		$dto->setTime(Input::get('startHour'), Input::get('startMinute'));
		$r->pocetak_rada = $dto->format('Y-m-d H:i:s');
		if($dto < new DateTime()){
			Session::flash('poruka', 'Zadani početak rada je prošao. Nije moguće napraviti rezervaciju u prošlosti.');
			return Redirect::route('Rezervacija.create')
			->withInput();
		}
		//provjera trajanja (količina i mjerna jedinica)
		$r->kolicina = Input::get('kolicina');
		if($r->kolicina < 1){
			Session::flash('poruka', 'Trajanje instrukcija mora biti barem 1 sat.');
			return Redirect::route('Rezervacija.create')
			->withInput();
		}
		$mjera = Mjera::find(Input::get('mjera'));
		if(!$mjera)
		{
			Session::flash('poruka', 'Odabrana mjera nije pronađena u sustavu.');
			return Redirect::route('Rezervacija.create')
			->withInput();
		}
		$r->mjera_id = $mjera->id;
		$dto->add(new DateInterval('PT'.($mjera->trajanje*$r->kolicina).'M'));
		$kraj_rada = $dto->format('Y-m-d H:i:s');
		$r->instruktor_id = Auth::id();

		//provjera zauzetosti instruktora
		$preklapanja = Rezervacija::where('instruktor_id', '=', $r->instruktor_id)
		->join('mjere', 'mjere.id', '=', 'rezervacije.mjera_id')
		->where('pocetak_rada', '<', $kraj_rada)
		->whereRaw("timestampadd(MINUTE, kolicina*trajanje, pocetak_rada) > '".$r->pocetak_rada."'")
		->count();
		if($preklapanja != 0)
		{
			Session::flash('poruka', 'U zadanome vremenu ste već zauzeti. Provjerite raspored.');
			return Redirect::route('Rezervacija.create')
			->withInput();
		}

		//provjera postojanja učionice
		$ucionica = Ucionica::find(Input::get('ucionica'));
		if(!$ucionica){
			Session::flash('poruka', 'Odabrana učionica nije pronađena u sustavu.');
			return Redirect::route('Rezervacija.create')
			->withInput();
		}

		//provjera kapaciteta učionice
		if($ucionica->max_broj_ucenika < $r->broj_ucenika){
			Session::flash('poruka', 'Odabrana učionica nema dovoljnu veličinu.');
			return Redirect::route('Rezervacija.create')
			->withInput();
		}

		//provjera zauzetosti učionice
		$preklapanja = Rezervacija::where('ucionica_id', '=', $ucionica->id)
		->join('mjere', 'mjere.id', '=', 'rezervacije.mjera_id')
		->where('pocetak_rada', '<', $kraj_rada)
		->whereRaw("timestampadd(MINUTE, kolicina*trajanje, pocetak_rada) > '".$r->pocetak_rada."'")
		->count();
		if($preklapanja==0)
		{
			$r->ucionica_id = $ucionica->id;
		}
		else
		{
			Session::flash('poruka', 'U zdanome vremenu je odabrana učionica zauzeta.');
			return Redirect::route('Rezervacija.create')
			->withInput();
		}

		if(Input::has('usmjerenje'))
			$r->usmjerenje = Input::get('usmjerenje');
		if(Input::has('predmet'))
			$r->predmet = Input::get('predmet');

		$r->save();
		return Redirect::route('Rezervacija.show', array($r->id));
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$this->layout->title = "Prikaz rezervacije";
		$this->layout->content =
		View::make('Rezervacija.show')
		->with('rezervacija', Rezervacija::find($id));
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
		$r = Rezervacija::find($id);
		if(!$r){
			Session::flash('poruka', 'Rezervacija nije pronađena u sustavu.');
			return Redirect::route('Instruktor.show', Auth::id());
		}
		if(strtotime($r->pocetak_rada) < time() && !Auth::user()->is_admin) {
			Session::flash('poruka', 'Samo je administratoru dozvoljeno ukloniti rezervaciju u prošlosti.');
			return Redirect::route('Rezervacija.show', $id);
		}
		$r->delete();
		Session::flash('poruka', 'Rezervacija je oslobođena.');
		return Redirect::route('Instruktor.show', Auth::id());
	}

	public function naplati($id)
	{
		$r = Rezervacija::find($id);
		if(strtotime($r->pocetak_rada) > time())
		{
			Session::flash('poruka', 'Nije moguće naplatiti instrukcije prije nego se odrade.');
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
			Session::flash('poruka', 'Nije moguće naplatiti instrukcije prije nego se odrade.');
			return Redirect::route('Rezervacija.show', $id);
		}

		if(!(Input::has('za_tvrtku')&& Input::has('ukupno_uplaceno')&&Input::get('za_instruktora')))
		{
			Session::flash('poruka', 'Niste unijeli sve potrebne podatke.');
			return Redirect::route('Rezervacija.naplati', $id)
			->withInput();
		}


		if(Input::get('za_tvrtku') < 0 || Input::get('za_instruktora') < 0)
		{
			Session::flash('poruka', 'Nepravilan unos. Iznosi en mogu biti negativni.');
			return Redirect::route('Rezervacija.naplati', $id)
			->withInput();
		}

		if(Input::get('za_tvrtku') + Input::get('za_instruktora') != Input::get('ukupno_uplaceno'))
		{
			Session::flash('poruka', 'Nepravilan unos. Ukupni iznos se dijeli na tvrtku i instruktora.');
			return Redirect::route('Rezervacija.naplati', $id)
			->withInput();
		}

		if(is_null($n))
		{
			$n = Naplata::create(Input::all());
			$n->save();
		}
		else $n->update(Input::all());
		return Redirect::route('Rezervacija.show', $id);
	}

	public function destroy_naplata($id)
	{
		Rezervacija::find($id)->naplata->delete();
		Session::flash('poruka', 'Naplata je uspješno uklonjena');
		return Redirect::route('Rezervacija.show', $id);
	}


}