<?php

class CjenovnikController extends \ResourceController {

    public function __construct() {
        parent::__construct();

        $this->requireManagePermission(Permission::PERMISSION_MANAGE_CJENOVNIK);
        $this->requireWatchPermission(Permission::PERMISSION_VIEW_CJENOVNIK);
        $this->requireDeletePermission(Permission::PERMISSION_REMOVE_CJENOVNIK);
        
        $this->beforeFilter(function() {
            if (!(Auth::check() && Auth::user()->hasPermission($this->watchPermissions))) {
                return Redirect::to('logout');
            }
        }, array('only' => array('_table')));
    }

    private function itemNotFound() {
        Session::flash(self::DANGER_MESSAGE_KEY, Cjenovnik::NOT_FOUND_MESSAGE);
        return Redirect::route('Cjenovnik.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        return View::make('Cjenovnik.index')
                        ->with('list', $this->_list());
    }

    public function _list($page = 1, $searchString = null) {
        if (!empty($searchString)) {
            $cjenovnici = Cjenovnik::where('ime', 'like', '%' . $searchString . '%')
                    ->orderBy('ime');
        } else
            $cjenovnici = Cjenovnik::orderBy('ime');
        if ($page != 1)
            Paginator::setCurrentPage($page);
        $cjenovnici = $cjenovnici->paginate(10);
        $v = View::make('Cjenovnik.list')
                ->with('cjenovnici', $cjenovnici);
        if (Request::ajax())
            return $v->renderSections()['list'];
        return $v;
    }
    
    public function _table($id = 1) {
        $cjenovnik = Cjenovnik::find($id);
        if (!$cjenovnik) {
            return Cjenovnik::NOT_FOUND_MESSAGE;
        }
        $v = View::make('Cjenovnik.table')
                ->with('cjenovnik', $cjenovnik);
        if (Request::ajax())
            return $v->renderSections()['table'];
        return $v;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        return View::make('Cjenovnik.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {
        $cjenovnik = new Cjenovnik();
        $error = $cjenovnik->getErrorOrSync(Input::all());
        if ($error != null)
            return Redirect::route('Cjenovnik.create')
                            ->withInput()
                            ->with(self::DANGER_MESSAGE_KEY, $error);
        return Redirect::route('Cjenovnik.show', array($cjenovnik->id));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        $cjenovnik = Cjenovnik::with('c_m_p')->find($id);
        if (!$cjenovnik)
            return $this->itemNotFound();
        return View::make('Cjenovnik.show')
                        ->with('cjenovnik', $cjenovnik);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $cjenovnik = Cjenovnik::find($id);
        if (!$cjenovnik)
            return $this->itemNotFound();
        return View::make('Cjenovnik.create')
                        ->with('cjenovnik', $cjenovnik);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {
        $cjenovnik = Cjenovnik::find($id);
        if (!$cjenovnik)
            return $this->itemNotFound();

        $error = $cjenovnik->getErrorOrSync(Input::all());
        if ($error != null)
            return Redirect::route('Cjenovnik.edit', array($id))
                            ->withInput()
                            ->with(self::DANGER_MESSAGE_KEY, $error);

        return Redirect::route('Cjenovnik.show', array($cjenovnik->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        $cjenovnik = Cjenovnik::find($id);
        if (!$cjenovnik)
            return $this->itemNotFound();
        $cjenovnik->delete();
        Session::flash(self::SUCCESS_MESSAGE_KEY, 'Cjenovnik je uspješno uklonjen!');
        return Redirect::route('Cjenovnik.index');
    }

}
