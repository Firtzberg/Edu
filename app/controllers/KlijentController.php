<?php

class KlijentController extends \BaseController {

	private function itemNotFound(){
		Session::flash(BaseController::DANGER_MESSAGE_KEY, Klijent::NOT_FOUND_MESSAGE);
		return Redirect::route('Klijent.index');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return View::make('klijent.index')
		->with('list', $this->_list());
	}

	public function _list($page = 1, $searchString = null){
		if(!empty($searchString)){
			$ucionice = Klijent::where('ime', 'like', '%'.$searchString.'%')
			->orderBy('ime');
		}
		else
			$ucionice = Klijent::orderBy('ime');
		if($page != 1)
			Paginator::setCurrentPage($page);
		$ucionice = $ucionice->paginate(10);
		$v = View::make('Klijent.list')
		->with('klijenti', $ucionice);
		if(Request::ajax())
			return $v->renderSections()['list'];
		return $v;
	}

	public function getSuggestionedKlijents(){
		if(Input::has('broj'))
		{
			$broj = (new Klijent())->getStorableBrojMobitela(Input::get('broj'));
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
			$item->broj_mobitela = $item->getReadableBrojMobitela();

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
		$klijent->broj_mobitela = $klijent->getStorableBrojMobitela($broj_mobitela);
		$klijent->ime =  Input::get('ime');
		$klijent->facebook = Input::get('facebook');
		$klijent->email = Input::get('email');
		$klijent->save();
		if(true)
		{
			Session::flash(self::SUCCESS_MESSAGE_KEY, 'Klijent je uspješno dodan.');
	  		return Redirect::route('Klijent.show', $klijent->broj_mobitela);
	  	}
	  	return Redirect::route('Klijent.create')
	  	->withInput()
	  	->withErrors($k->errors());
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
		return View::make('Klijent.show')
		->with('klijent', $klijent);
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
	  		$klijent->broj_mobitela = $klijent->getStorableBrojMobitela($broj_mobitela);
		$klijent->ime =  Input::get('ime');
		$klijent->facebook = Input::get('facebook');
		$klijent->email = Input::get('email');
		$klijent->save();
		if(true)
		{
			Session::flash(self::SUCCESS_MESSAGE_KEY,'Klijent uspješno uređen');
			return Redirect::route('Klijent.show', array($broj_mobitela));
		}

		return Redirect('Klijent.edit')
		->withInput()
		->withErrors($klijent->errors());
	}

}
