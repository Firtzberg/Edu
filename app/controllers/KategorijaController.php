<?php

class KategorijaController extends \ResourceController {
    
    public function __construct() {
        parent::__construct();
        
        $this->requireManagePermission(Permission::PERMISSION_MANAGE_PREDMET_KATEGORIJA);
        $this->requireWatchPermission(Permission::PERMISSION_MANAGE_PREDMET_KATEGORIJA);
        $this->requireDeletePermission(Permission::PERMISSION_REMOVE_PREDMET_KATEGORIJA);
    }

    private function itemNotFound(){
		Session::flash(self::DANGER_MESSAGE_KEY, Kategorija::NOT_FOUND_MESSAGE);
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
			return Redirect::route('Kategorija.show', array($nadkategorija_id));
		}
		if($nadkategorija->podkategorije()->where('ime', '=', $ime)->count() > 0){
			Session::flash(self::DANGER_MESSAGE_KEY, 'Kategorija '.$nadkategorija->ime.' već ima podkategoriju s imenom '.$ime.'.');
			return Redirect::route('Kategorija.show', array($nadkategorija_id));
		}

		$kategorija = new Kategorija();
		$kategorija->ime = $ime;
		$nadkategorija->podkategorije()->save($kategorija);
		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Kategorija uspješno dodana.');
		return Redirect::route('Kategorija.show', array($kategorija->id));
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
    public function show($id) {
        $kategorija = Kategorija::with('predmeti', 'podkategorije')->find($id);
        if (!$kategorija) {
            return $this->itemNotFound();
        }
        return View::make('Kategorija.show')
                        ->with('kategorija', $kategorija);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $kategorija = Kategorija::find($id);
        //provjera postojanja
        if (!$kategorija)
            return $this->itemNotFound();

        return View::make('Kategorija.edit')
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
			return Redirect::route('Kategorija.edit', array($id))
                        ->withInput();
		}
		$kategorija = Kategorija::with('nadkategorija')->find($id);
		$nadkategorija = $kategorija->nadkategorija;
		if(!$kategorija)
			return $this->itemNotFound();
		if($nadkategorija->podkategorije()->where('ime', '=', $ime)->where('id', '!=', $id)->count() > 0){
			Session::flash(self::DANGER_MESSAGE_KEY, 'Kategorija '.$nadkategorija->ime.' već ima podkategoriju s imenom '.$ime.'.');
			return Redirect::route('Kategorija.edit', array($id))
                        ->withInput();
		}
		$kategorija->ime = $ime;
		$kategorija->save();
		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Kategorija je uspješno preimenovana.');
		return Redirect::route('Kategorija.show', array($id));
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
		return Redirect::route('Kategorija.show', array($nadkategorija_id));
	}

        /**
         * 
         * @param int $id
         * @return Response
         */
	public function getChildren($user_id, $id = null) {
        if ($id) {
            $kategorija = Kategorija::find($id);
        } else {
            $kategorija = Kategorija::whereRaw('id = nadkategorija_id')
                    ->first();
        }
        if (!$kategorija) {
            return $this->itemNotFound();
        }
        return Response::json($kategorija->getHierarchyFor($user_id));
    }

}
