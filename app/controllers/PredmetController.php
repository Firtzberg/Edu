<?php

namespace App\Controller;

use App\Model\Permission;
use App\Model\Predmet;
use Redirect;
use Session;

class PredmetController extends App\Controller\ResourceController {
    
    public function __construct() {
        parent::__construct();
        
        $this->requireManagePermission(Permission::PERMISSION_MANAGE_PREDMET_KATEGORIJA);
        $this->requireWatchPermission(Permission::PERMISSION_MANAGE_PREDMET_KATEGORIJA);
        $this->requireDeletePermission(Permission::PERMISSION_REMOVE_PREDMET_KATEGORIJA);
    }

	private function itemNotFound(){
		Session::flash(self::DANGER_MESSAGE_KEY, Predmet::NOT_FOUND_MESSAGE);
		return Redirect::route('Kategorija.index');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($kategorija_id)
	{
		return View::make('Predmet.create')
		->with('kategorija_id', $kategorija_id);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$predmet = new Predmet();
		$error = $predmet->getErrorOrSync(Input::all());
		if($error){
			Session::flash(self::DANGER_MESSAGE_KEY, $error);
			if(Input::has('kategorija_id'))
				return Redirect::route('Predmet.create', array('kategorija_id' => Input::get('kategorija_id')))
					->withInput();
			else return Redirect::route('Kategorija.index');
		}
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
		return View::make('Predmet.show')
		->with('predmet', $predmet);
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
		return View::make('Predmet.create')
		->with('predmet', $predmet);
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

		$error = $predmet->getErrorOrSync(Input::all());
		if($error){
			Session::flash(self::DANGER_MESSAGE_KEY, $error);
			return Redirect::route('Predmet.edit', array('id' => $id))
			->withInput();
		}
		
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
}
