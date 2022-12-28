<?php

class NeradniDanController extends \ResourceController {
    
    public function __construct() {
        parent::__construct();
        
        $this->requireManagePermission(Permission::PERMISSION_MANAGE_NERADNI_DAN);
        $this->requireWatchPermission(Permission::PERMISSION_VIEW_NERADNI_DAN);
        $this->requireDeletePermission(Permission::PERMISSION_MANAGE_NERADNI_DAN);
    }

    private function itemNotFound(){
		Session::flash(self::DANGER_MESSAGE_KEY, NeradniDan::NOT_FOUND_MESSAGE);
  		return Redirect::route('NeradniDan.index');
    }
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return View::make('NeradniDan.index')
		->with('list', $this->_list());
	}

	public function _list($page = 1, $searchString = null){
		if(!empty($searchString)){
			$neradniDani = NeradniDan::where('naziv', 'like', '%'.$searchString.'%')
			->orderBy('godina', 'desc')
            ->orderBy('mjesec', 'desc')
            ->orderBy('dan', 'desc');
		}
		else
            $neradniDani = NeradniDan::orderBy('godina', 'desc')
                ->orderBy('mjesec', 'desc')
                ->orderBy('dan', 'desc');
		if($page != 1)
			Paginator::setCurrentPage($page);
        $neradniDani = $neradniDani->paginate(10);
		$v = View::make('NeradniDan.list')
		->with('neradniDani', $neradniDani);
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
		return View::make('NeradniDan.create');
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
    public function store() {
        $neradniDan = new NeradniDan();
        $error = $neradniDan->getErrorOrSync(Input::all());
        if ($error) {
            Session::flash(self::DANGER_MESSAGE_KEY, $error);
            return Redirect::route('NeradniDan.create')
                            ->withInput();
        }
        Session::flash(self::SUCCESS_MESSAGE_KEY, 'Neradni dan uspješno dodana');
        return Redirect::route('NeradniDan.index');
    }


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        $neradniDan = NeradniDan::find($id);
		if(!$neradniDan)
			return $this->itemNotFound();
		return View::make('NeradniDan.create')
		->with('neradniDan', $neradniDan);
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
    public function update($id) {
        $neradniDan = NeradniDan::find($id);
        if (!$neradniDan)
            return $this->itemNotFound();
        $error = $neradniDan->getErrorOrSync(Input::all());
        if ($error) {
            Session::flash(self::DANGER_MESSAGE_KEY, $error);
            return Redirect::route('NeradniDan.edit', array($id))
                            ->withInput();
        }
        Session::flash(self::SUCCESS_MESSAGE_KEY, 'Neradni dan uspješno uređen');
        return Redirect::route('NeradniDan.index');
    }

    /**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
        $neradniDan = NeradniDan::find($id);
		if(!$neradniDan)
			return $this->itemNotFound();
        $neradniDan->delete();
		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Neradni dan je uspješno uklonjen!');
		return Redirect::route('NeradniDan.index');
	}
}