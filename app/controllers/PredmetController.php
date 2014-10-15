<?php

class PredmetController extends \BaseController {

	protected $layout = 'layouts.master';

	private function itemNotFound(){
		Session::flash(BaseController::DANGER_MESSAGE_KEY, Predmet::NOT_FOUND_MESSAGE);
		return Redirect::route('Kategorija.index');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($kategorija_id)
	{
		$this->layout->title = "Dodaj predmet";
		$this->layout->content =
		View::make('Predmet.create')
		->with('kategorija_id', $kategorija_id);
		return $this->layout;
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$ime = Input::get('ime');
		$kategorija_id = Input::get('kategorija_id');
		$kategorija = Kategorija::find($kategorija_id);
		if(!$kategorija_id){
			Session::flash(self::DANGER_MESSAGE_KEY, 'Nije zadana kategorija predmeta.');
			return Redirect::route('Kategorija.index');
		}
		if(!$kategorija){
			Session::flash(self::DANGER_MESSAGE_KEY, Kategorija::NOT_FOUND_MESSAGE);
			return Redirect::route('Kategorija.index');
		}
		if(empty($ime)){
			Session::flash(self::DANGER_MESSAGE_KEY, 'Ime predmeta je obvezno.');
			return Redirect::route('Predmet.create', array('kategorija_id' => $kategorija_id))
			->withInput();
		}
		if($kategorija->predmeti()->where('ime', '=', $ime)->count() > 0){
			Session::flash(self::DANGER_MESSAGE_KEY, 'U kategoriji '.$kategorija->ime.' već postoji predmet s imenom '.$ime.'.');
			return Redirect::route('Predmet.create', array('kategorija_id' => $kategorija_id))
			->withInput();
		}

		$predmet = new Predmet();
		$predmet->ime = $ime;

		$mjereSyncronizator = $predmet->getErrorOrCijenaSyncArray(Input::all());
		if(!is_array($mjereSyncronizator)){
			Session::flash(self::DANGER_MESSAGE_KEY, $mjereSyncronizator);
			return Redirect::route('Predmet.create', array('kategorija_id' => $kategorija_id))
			->withInput();
		}

		$kategorija->predmeti()->save($predmet);
		$predmet->cijene()->sync($mjereSyncronizator);-
		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Predmet je uspješno dodan.');
		return Redirect::route('Predmet.show', array('id' => $predmet->id));
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$predmet = Predmet::with('cijene')->find($id);
		if(!$predmet)
			return $this->itemNotFound();
		$this->layout->title = $predmet->ime.' - Predmet';
		$this->layout->content = View::make('Predmet.show')
		->with('predmet', $predmet);
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
		$predmet = Predmet::with('cijene')->find($id);
		if(!$predmet)
			return $this->itemNotFound();
		$this->layout->title = $predmet->ime." - Uredi predmet";
		$this->layout->content =
		View::make('Predmet.create')
		->with('predmet', $predmet);
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
		$predmet = Predmet::with('cijene', 'kategorija')->find($id);
		if(!$predmet)
			return $this->itemNotFound();

		$ime = Input::get('ime');
		if(empty($ime)){
			Session::flash(self::DANGER_MESSAGE_KEY, 'Ime predmeta je obvezno.');
			return Redirect::route('Predmet.edit', array('id' => $id))
			->withInput();
		}
		if($predmet->kategorija->predmeti()->where('ime', '=', $ime)->where('id', '!=', $id)->count() > 0){
			Session::flash(self::DANGER_MESSAGE_KEY, 'U kategoriji '.$predmet->kategorija->ime.' već postoji predmet s imenom '.$ime.'.');
			return Redirect::route('Predmet.edit', array('id' => $id))
			->withInput();
		}

		$predmet->ime = $ime;

		$mjereSyncronizator = $predmet->getErrorOrCijenaSyncArray(Input::all());
		if(!is_array($mjereSyncronizator)){
			Session::flash(self::DANGER_MESSAGE_KEY, $mjereSyncronizator);
			return Redirect::route('Predmet.edit', array('id' => $id))
			->withInput();
		}

		$predmet->save();
		$predmet->cijene()->sync($mjereSyncronizator);
		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Predmet je uspješno uređen.');
		return Redirect::route('Predmet.show', array('id' => $id));
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$predmet = Predmet::find($id);
		if(!$predmet)
			return $this->itemNotFound();
		$kategorija_id = $predmet->kategorija_id;
		$predmet->delete();
		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Predmet je uspješno uklonjen!');
		return Redirect::route('Kategorija.show', array('id' => $kategorija_id));
	}

	/**
	 * Display root category.
	 *
	 * @return Response
	 */
	public function enable($id)
	{
		$predmet = Predmet::find($id);
		if(!$predmet)
			return $this->itemNotFound();
		$predmet->enabled = true;
		$predmet->save();
		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Predmet je vidljiv.');
		return Redirect::route('Predmet.show', array('id' => $id));
	}

	/**
	 * Display root category.
	 *
	 * @return Response
	 */
	public function disable($id)
	{
		$predmet = Predmet::find($id);
		if(!$predmet)
			return $this->itemNotFound();
		$predmet->enabled = false;
		$predmet->save();
		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Predmet je uspješno skriven.');
		return Redirect::route('Predmet.show', array('id' => $id));
	}
}
