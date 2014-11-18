<?php

class UcionicaController extends \ResourceController {
    
    public function __construct() {
        parent::__construct();
        
        $this->requireManagePermission(Permission::PERMISSION_MANAGE_UCIONICA);
        $this->requireWatchPermission(Permission::PERMISSION_VIEW_UCIONICA);
        $this->requireDeletePermission(Permission::PERMISSION_REMOVE_UCIONICA);
    }

    private function itemNotFound(){
		Session::flash(self::DANGER_MESSAGE_KEY, Ucionica::NOT_FOUND_MESSAGE);
  		return Redirect::route('Ucionica.index');
    }
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return View::make('Ucionica.index')
		->with('list', $this->_list());
	}

	public function _list($page = 1, $searchString = null){
		if(!empty($searchString)){
			$ucionice = Ucionica::where('naziv', 'like', '%'.$searchString.'%')
			->orderBy('naziv');
		}
		else
			$ucionice = Ucionica::orderBy('naziv');
		if($page != 1)
			Paginator::setCurrentPage($page);
		$ucionice = $ucionice->paginate(10);
		$v = View::make('Ucionica.list')
		->with('ucionice', $ucionice);
		if(Request::ajax())
			return $v->renderSections()['list'];
		return $v;
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('Ucionica.create');
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
    public function store() {
        $ucionica = new Ucionica();
        $error = $ucionica->getErrorOrSync(Input::all());
        if ($error) {
            Session::flash(self::DANGER_MESSAGE_KEY, $error);
            return Redirect::route('Ucionica.create')
                            ->withInput();
        }
        Session::flash(self::SUCCESS_MESSAGE_KEY, 'Učionica uspješno dodana');
        return Redirect::route('Ucionica.show', array($ucionica->id));
    }

    /**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id, $tjedan = null, $godina = null)
	{
		$ucionica = Ucionica::find($id);
		if(!$ucionica)
			return $this->itemNotFound();
		return View::make('Ucionica.show')
		->with('ucionica', $ucionica)
		->with('raspored', $this->raspored($id, $tjedan, $godina));
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$ucionica = Ucionica::find($id);
		if(!$ucionica)
			return $this->itemNotFound();
		return View::make('Ucionica.create')
		->with('ucionica', $ucionica);
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
    public function update($id) {
        $ucionica = Ucionica::find($id);
        if (!$ucionica)
            return $this->itemNotFound();
        $error = $ucionica->getErrorOrSync(Input::all());
        if ($error) {
            Session::flash(self::DANGER_MESSAGE_KEY, $error);
            return Redirect::route('Ucionica.edit', array($id))
                            ->withInput();
        }
        Session::flash(self::SUCCESS_MESSAGE_KEY, 'Učionica uspješno uređena');
        return Redirect::route('Ucionica.show', array($id));
    }

    /**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$ucionica = Ucionica::find($id);
		if(!$ucionica)
			return $this->itemNotFound();
		$ucionica->delete();
		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Učionica je uspješno uklonjena!');
		return Redirect::route('Ucionica.index');
	}

	public function raspored($id, $tjedan = null, $godina = null)
	{
		if(is_null($tjedan))
			$tjedan = date('W');
		if(is_null($godina))
			$godina = date('o');
		$t = new DateTime();
		$t->setISODate($godina, $tjedan);
		if($tjedan <1 || $tjedan > 51)
		{
			$tjedan = $t->format('W');
			$godina = $t->format('o');
		}

		$ucionica = Ucionica::find($id);

		return View::make('Ucionica.raspored')
		->with('tjedan', $tjedan)
		->with('godina', $godina)
		->with('ucionica', $ucionica)
		->with('strana_rasporeda', \Helpers\Raspored::RasporedForUcionicaInWeek($id, $tjedan, $godina));
	}
}