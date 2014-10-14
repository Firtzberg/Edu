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
		$mjere = Mjera::all();
		$cijene = array();
		foreach ($mjere as $mjera) {
			$cijena = array();

			if(Input::has('individualno-cijena-'.$mjera->id)){
				$value = Input::get('individualno-cijena-'.$mjera->id);
				if($value < 0){
					Session::flash(self::DANGER_MESSAGE_KEY, 'Individualna cijena za '.$mjera->znacenje.' ne može biti negativna.');
					return Redirect::route('Predmet.create', array('kategorija_id' => $kategorija_id))
					->withInput();
				}
				$cijena['individualno'] = $value;
			}
			else $cijena['individualno'] = 0;
			if(Input::has('popust-cijena-'.$mjera->id)){
				$value = Input::get('popust-cijena-'.$mjera->id);
				if($value < 0){
					Session::flash(self::DANGER_MESSAGE_KEY, 'Popust po dodatnoj osobi za '.$mjera->znacenje.' ne može biti negativan.');
					return Redirect::route('Predmet.create', array('kategorija_id' => $kategorija_id))
					->withInput();
				}
				$cijena['popust'] = $value;
			}
			else $cijena['popust'] = 0;
			if(Input::has('minimalno-cijena-'.$mjera->id)){
				$value = Input::get('minimalno-cijena-'.$mjera->id);
				if($value < 0){
					Session::flash(self::DANGER_MESSAGE_KEY, 'Minimalna cijena za '.$mjera->znacenje.' ne može biti negativan.');
					return Redirect::route('Predmet.create', array('kategorija_id' => $kategorija_id))
					->withInput();
				}
				if($cijena['individualno'] < $value){
					Session::flash(self::DANGER_MESSAGE_KEY, 'Minimalna cijena za '.$mjera->znacenje.' ne može biti manja od individualne cijene.');
					return Redirect::route('Predmet.create', array('kategorija_id' => $kategorija_id))
					->withInput();
				}
				$cijena['minimalno'] = $value;
			}
			else $cijena['minimalno'] = 0;

			$cijene[$mjera->id] = $cijena;
		}
		$kategorija->predmeti()->save($predmet);
		$predmet->cijene()->attach($cijene);
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
		foreach ($predmet->cijene as $cijena) {
			if(Input::has('individualno-cijena-'.$cijena->id)){
				$value = Input::get('individualno-cijena-'.$cijena->id);
				if($value < 0){
					Session::flash(self::DANGER_MESSAGE_KEY, 'Individualna cijena za '.$cijena->znacenje.' ne može biti negativna.');
					return Redirect::route('Predmet.edit', array('id' => $id))
					->withInput();
				}
				$cijena->pivot->individualno = $value;
			}
			else $cijena->pivot->individualno = 0;
			if(Input::has('popust-cijena-'.$cijena->id)){
				$value = Input::get('popust-cijena-'.$cijena->id);
				if($value < 0){
					Session::flash(self::DANGER_MESSAGE_KEY, 'Popust po dodatnoj osobi za '.$cijena->znacenje.' ne može biti negativan.');
					return Redirect::route('Predmet.edit', array('id' => $id))
					->withInput();
				}
				$cijena->pivot->popust = $value;
			}
			else $cijena->pivot->minimalno = 0;
			if(Input::has('minimalno-cijena-'.$cijena->id)){
				$value = Input::get('minimalno-cijena-'.$cijena->id);
				if($value < 0){
					Session::flash(self::DANGER_MESSAGE_KEY, 'Minimalna cijena za '.$cijena->znacenje.' ne može biti negativan.');
					return Redirect::route('Predmet.edit', array('id' => $id))
					->withInput();
				}
				if($cijena->pivot->individualno < $value){
					Session::flash(self::DANGER_MESSAGE_KEY, 'Minimalna cijena za '.$cijena->znacenje.' ne može biti manja od individualne cijene.');
					return Redirect::route('Predmet.edit', array('id' => $id))
					->withInput();
				}
				$cijena->pivot->minimalno = $value;
			}
			else $cijena->pivot->minimalno = 0;
		}
		$predmet->push();
		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Predmet je uspješno Uređen.');
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
