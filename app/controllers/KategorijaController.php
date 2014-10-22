<?php

class KategorijaController extends \BaseController {

	private function itemNotFound(){
		Session::flash(BaseController::DANGER_MESSAGE_KEY, Kategorija::NOT_FOUND_MESSAGE);
		return View::make('Kategorija.index');
	}

	/**
	 * Display root category.
	 *
	 * @return Response
	 */
	public function index()
	{
		$kategorija = Kategorija::whereRaw('id = nadkategorija_id')
		->with('podkategorije', 'predmeti')
		->first();
		return View::make('Kategorija.show')
		->with('kategorija', $kategorija);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$ime = Input::get('ime');
		$nadkategorija_id = Input::get('nadkategorija_id');
		if(!$nadkategorija_id){
			Session::flash(self::DANGER_MESSAGE_KEY, 'Nije zadana nadkategorija kategorije.');
			return Redirect::route('Kategorija.index');
		}
		$nadkategorija = Kategorija::find($nadkategorija_id);
		if(!$nadkategorija){
			Session::flash(self::DANGER_MESSAGE_KEY, 'Nadkategorija nije pronađena u sustavu.');
			return Redirect::route('Kategorija.index');
		}
		if(empty($ime)){
			Session::flash(self::DANGER_MESSAGE_KEY, 'Ime kategorije je obvezno.');
			return Redirect::route('Kategorija.show', array('id' => $nadkategorija_id));
		}
		if($nadkategorija->podkategorije()->where('ime', '=', $ime)->count() > 0){
			Session::flash(self::DANGER_MESSAGE_KEY, 'Kategorija '.$nadkategorija->ime.' već ima podkategoriju s imenom '.$ime.'.');
			return Redirect::route('Kategorija.show', array('id' => $nadkategorija_id));
		}

		$kategorija = new Kategorija();
		$kategorija->ime = $ime;
		$nadkategorija->podkategorije()->save($kategorija);
		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Kategorija uspješno dodana.');
		return Redirect::route('Kategorija.show', array('id' => $kategorija->id));
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$kategorija = Kategorija::with('predmeti', 'podkategorije')->find($id);
		if(!$kategorija)
			return $this->itemNotFound();
		return View::make('Kategorija.show')
		->with('kategorija', $kategorija);
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$ime = Input::get('ime');
		if(!$ime){
			Session::flash(self::DANGER_MESSAGE_KEY, 'Ime kategorije je obvezno.');
			return Redirect::route('Kategorija.show', array('id' => $id));
		}
		$kategorija = Kategorija::find($id);
		if(!$kategorija)
			return $this->itemNotFound();
		$kategorija->ime = $ime;
		$kategorija->save();
		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Kategorija je uspješno preimenovana.');
		return Redirect::route('Kategorija.show', array('id' => $id));
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$kategorija = Kategorija::find($id);
		if(!$kategorija)
			return $this->itemNotFound();
		$nadkategorija_id = $kategorija->nadkategorija_id;
		$kategorija->delete();
		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Kategorija je uspješno uklonjena!');
		return Redirect::route('Kategorija.show', array('id' => $nadkategorija_id));
	}

	/**
	 * Enable category.
	 *
	 * @return Response
	 */
	public function enable($id)
	{
		$kategorija = Kategorija::find($id);
		if(!$kategorija)
			return $this->itemNotFound();
		$kategorija->enabled = true;
		$kategorija->save();
		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Kategorija je vidljiva.');
		return Redirect::route('Kategorija.show', array('id' => $id));
	}

	/**
	 * Disable category.
	 *
	 * @return Response
	 */
	public function disable($id)
	{
		$kategorija = Kategorija::find($id);
		if(!$kategorija)
			return $this->itemNotFound();
		$kategorija->enabled = false;
		$kategorija->save();
		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Kategorija je uspješno skrivena.');
		return Redirect::route('Kategorija.show', array('id' => $id));
	}

	public function getChildren($id){
		$kategorija = Kategorija::find($id);
		if(!$kategorija)
			return $this->itemNotFound();
		return Response::json($kategorija->getEnabledChildren());
	}

}
