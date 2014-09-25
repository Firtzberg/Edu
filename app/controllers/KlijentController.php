<?php

class KlijentController extends \BaseController {

	protected $layout = "layouts.master";

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$this->layout->title = 'Popis Klijenata';
		if(Input::has('searchString')){
			$klijenti = Klijent::where('ime', 'like', '%'.Input::get('searchString').'%')
			->orderBy('ime');
			Input::flash();
		}
		else
			$klijenti = Klijent::orderBy('ime');
		$klijenti = $klijenti->paginate(10);
		$this->layout->content = View::make('klijent.index')
		->with('klijenti', $klijenti);
		return $this->layout;
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$this->layout->title = "Dodaj klijenta";
		$this->layout->content =
		View::make('Klijent.create');
		return $this->layout;
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$k = Klijent::create(Input::all());
		$k->save();
		if(true)
		{
			Session::flash('poruka', 'Klijent je uspješno dodan.');
	  		return Redirect::route('Klijent.index');
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
		$k = Klijent::find($id);
		if(!$k){
			Session::flash('poruka', 'Klijent nije pronađen u sustavu.');
	  		return Redirect::route('Klijent.index');
		}
		$this->layout->title = $k->ime." - Kijent";
		$this->layout->content =
		View::make('Klijent.show')
		->with('klijent', $k);
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
		$k = Klijent::find($id);
		if(!$k){
			Session::flash('poruka', 'Klijent nije pronađen u sustavu.');
	  		return Redirect::route('Klijent.index');
		}
		$this->layout->title = $k->ime." - Uredi klijenta";
		$this->layout->content =
		View::make('Klijent.create')
		->with('klijent', $k);
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
		$k = Klijent::find($id);
		$k->update(Input::all());
		if(true)
		{
			Session::flash('poruka','Klijent uspješno uređen');
			return Redirect::route('Klijent.show', array($id));
		}

		return Redirect('Klijent.edit')
		->withInput()
		->withErrors($k->errors());
	}

}
