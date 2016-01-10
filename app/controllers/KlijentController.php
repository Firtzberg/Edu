<?php

class KlijentController extends \ResourceController {
    private $polazniciQuery = '(select COUNT(*) from klijent_rezervacija where missed = FALSE AND klijent_rezervacija.rezervacija_id = rezervacije.id)';
    public function __construct() {
        parent::__construct();
        $this->requireWatchPermission(Permission::PERMISSION_VIEW_KLIJENT);
        $this->requireManagePermission(Permission::PERMISSION_MANAGE_KLIJENT);
        $this->beforeFilter(function() {
            if (!(Auth::check() && Auth::user()->hasPermission(Permission::PERMISSION_VIEW_KLIJENT))) {
                return Redirect::to('logout');
            }
        }, array('only' => array('rezervacije')));
    }

	private function itemNotFound(){
		Session::flash(self::DANGER_MESSAGE_KEY, Klijent::NOT_FOUND_MESSAGE);
		return Redirect::route('Klijent.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return View::make('Klijent.index')
		->with('list', $this->_list());
	}

	public function _list($page = 1, $searchString = null){
		if(!empty($searchString)){
			$klijenti = Klijent::where('ime', 'like', '%'.$searchString.'%')
			->orderBy('ime');
		}
		else
			$klijenti = Klijent::orderBy('ime');
		if($page != 1)
			Paginator::setCurrentPage($page);
		$klijenti = $klijenti->paginate(10);
		$v = View::make('Klijent.list')
		->with('klijenti', $klijenti);
		if(Request::ajax())
			return $v->renderSections()['list'];
		return $v;
	}

	public function getSuggestionedKlijents(){
		if(Input::has('broj'))
		{
			$broj = Klijent::getStorableBrojMobitela(Input::get('broj'));
		}
		$query = Auth::user()->klijenti();
		if(isset($broj)){
			$query->where('klijenti.broj_mobitela', 'like', $broj.'%');
		}
		if(Input::has('ime')){
			$query->where('klijenti.ime', 'like', '%'.Input::get('ime').'%');
		}
		$collection = $query->take(5)->get();
		if($collection->count() < 1)
		{
			$query = Klijent::select('broj_mobitela', 'ime');
			if(isset($broj)){
				$query->where('klijenti.broj_mobitela', 'like', $broj.'%');
			}
			if(Input::has('ime')){
				$query->where('klijenti.ime', 'like', '%'.Input::get('ime').'%');
			}
			$collection = $query->take(5)->get();
		}
		foreach ($collection as $item) 
			$item->broj_mobitela = Klijent::getReadableBrojMobitela($item->broj_mobitela);

		return Response::json($collection);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('Klijent.create');
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		if(!Input::has('broj_mobitela'))
		{
			Session::flash(self::DANGER_MESSAGE_KEY, 'Broj mobitela je obvezan.');
	  		return Redirect::route('Klijent.create')
	  		->withInput();
	  	}
		if(!Input::has('ime'))
		{
			Session::flash(self::DANGER_MESSAGE_KEY, 'Ime i prezuime su obvezni.');
	  		return Redirect::route('Klijent.create')
	  		->withInput();
	  	}

		$broj_mobitela = Input::get('broj_mobitela');

		if(Klijent::find($broj_mobitela))
		{
			Session::flash(self::DANGER_MESSAGE_KEY, 'U sustavu već postoji klijent s unešenim brojem.');
	  		return Redirect::route('Klijent.create')
	  		->withInput();
	  	}

		$klijent = new Klijent();
		$klijent->broj_mobitela = Klijent::getStorableBrojMobitela($broj_mobitela);
		$klijent->ime =  Input::get('ime');
		$klijent->skola = Input::get('skola');
		if (Input::get('razred')) {
                    $klijent->razred = Input::get('razred');
                }
		$klijent->facebook = Input::get('facebook');
		$klijent->email = Input::get('email');
		$klijent->roditelj = Input::get('roditelj');
		$klijent->broj_roditelja = Klijent::getStorableBrojMobitela(Input::get('broj_roditelja'));
		$klijent->save();
		if(true)
		{
			Session::flash(self::SUCCESS_MESSAGE_KEY, 'Klijent je uspješno dodan.');
	  		return Redirect::route('Klijent.show', $klijent->broj_mobitela);
	  	}
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$klijent = Klijent::find($id);
		if(!$klijent)
			return $this->itemNotFound();
                
                $jePlatio = 'missed = FALSE AND ukupno_uplaceno IS NOT NULL';
        $interesi = $klijent->rezervacije()
                ->leftJoin('naplate', 'naplate.rezervacija_id', '=', 'rezervacije.id')
                ->groupBy('instruktor_id', 'predmet_id')
                ->select(array(
                    DB::Raw('COUNT(*) as count'),
                    DB::Raw('COUNT(CASE WHEN ' . $jePlatio . ' THEN 1 END) as naplate_count'),
                    DB::Raw('COUNT(CASE WHEN missed = TRUE THEN 1 END) as missed_count'),
                    DB::Raw('SUM(CASE WHEN ' . $jePlatio . ' THEN ukupno_uplaceno / ' . $this->polazniciQuery . ' END) as ukupno'),
                    DB::Raw('SUM(CASE WHEN ' . $jePlatio . ' THEN za_tvrtku / ' . $this->polazniciQuery . ' END) as za_tvrtku'),
                    DB::Raw('MAX(pocetak_rada) as posljednji_put'),
                ))
                ->orderBy('posljednji_put', 'DESC')
                ->get();
        return View::make('Klijent.show')
                        ->with('klijent', $klijent)
                        ->with('interesi', $interesi);
	}
        
	/**
     * Display the Rezervacije of a Klijent for a specific User and Predmet.
     *
     * @param  int  $id Id of the Klijent
     * @param  int  $user_id Id of the User to which all Rezervacije belong
     * @param  int  $predmet_id Id of the Predmet to which all Rezervacije belong.
     * @return Response
     */
    public function rezervacije($id, $user_id, $predmet_id) {
        $klijent = Klijent::find($id);
        if (!$klijent)
            return $this->itemNotFound();
        $predmet = Predmet::find($predmet_id);
        $user = User::find($user_id);
        $rezervacije = $klijent->rezervacije()
                ->with('naplata')
                ->with('naplata.stvarnaMjera')
                ->where('instruktor_id', $user_id)
                ->where('predmet_id', $predmet_id)
                ->orderBy('pocetak_rada', 'DESC')
                ->select(DB::Raw($this->polazniciQuery . ' as polaznici_count'))
                ->get();
        return View::make('Klijent.rezervacije')
                        ->with('klijent', $klijent)
                        ->with('user', $user)
                        ->with('predmet', $predmet)
                        ->with('rezervacije', $rezervacije);
    }

    /**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$klijent = Klijent::find($id);
		if(!$klijent)
			return $this->itemNotFound();
		return View::make('Klijent.create')
		->with('klijent', $klijent);
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		if(!Input::has('broj_mobitela'))
		{
			Session::flash(self::DANGER_MESSAGE_KEY, 'Broj mobitela je obvezan.');
	  		return Redirect::route('Klijent.edit')
	  		->withInput();
	  	}
		if(!Input::has('ime'))
		{
			Session::flash(self::DANGER_MESSAGE_KEY, 'Ime i prezuime su obvezni.');
	  		return Redirect::route('Klijent.edit')
	  		->withInput();
	  	}

		$broj_mobitela = Input::get('broj_mobitela');

		$klijent = Klijent::find($id);
		if(!$klijent)
			return $this->itemNotFound();

	  	if($broj_mobitela != $id)
	  		$klijent->broj_mobitela = Klijent::getStorableBrojMobitela($broj_mobitela);
		$klijent->ime =  Input::get('ime');
		$klijent->skola = Input::get('skola');
		if (Input::get('razred')) {
                    $klijent->razred = Input::get('razred');
                }
		$klijent->facebook = Input::get('facebook');
		$klijent->email = Input::get('email');
		$klijent->roditelj = Input::get('roditelj');
		$klijent->broj_roditelja = Klijent::getStorableBrojMobitela(Input::get('broj_roditelja'));
		$klijent->save();
		if(true)
		{
			Session::flash(self::SUCCESS_MESSAGE_KEY,'Klijent uspješno uređen');
			return Redirect::route('Klijent.show', array($klijent->broj_mobitela));
		}

		return Redirect('Klijent.edit')
		->withInput()
		->withErrors($klijent->errors());
	}

}
