<?php

class DjelatnikController extends \ResourceController {
    
    public function __construct() {
        parent::__construct();

        $this->requireDeletePermission(Permission::PERMISSION_REMOVE_USER);
        $this->beforeFilter(function() {
            if (!Auth::user()->hasPermission(Permission::PERMISSION_VIEW_USER)) {
                return Redirect::to('logout');
            }
        }, array('only' => array('index', '_list')));
        
        $this->beforeFilter(function() {
            if (!Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_USER)) {
                return Redirect::to('logout');
            }
        }, array('only' => array('create', 'store')));

        $this->beforeFilter('ViewProfile', array('only' =>
            array('show')));

        $this->beforeFilter('ManageProfile', array('only' =>
            array('changePassword', 'postChangePassword', 'edit', 'update')));

        $this->beforeFilter(function($route) {
            if (!(Auth::user()->hasPermission(Permission::PERMISSION_PASSWORD_RESET) ||
                    Auth::id() == $route->getParameter('Djelatnik'))) {
                return Redirect::to('logout');
            }
        }, array('only' => array('changePassword', 'postChangePassword')));
    }

    private function itemNotFound() {
        Session::flash(self::DANGER_MESSAGE_KEY, User::NOT_FOUND_MESSAGE);
        return Redirect::route('Djelatnik.index');
    }

    public function signIn() {
        if (Auth::check()) {
            return Redirect::route('home');
        }
        return View::make('signIn');
    }

    public function login() {
        if (Auth::check()&&Input::has('userName')) {
            if (Auth::user()->name == Input::get('userName')) {
                return Redirect::route('home')
                                ->with(self::SUCCESS_MESSAGE_KEY, 'Već si bio registriran');
            }
        }
        $remember = Input::get('remember');

        if (Input::has('userName') && Input::has('lozinka') &&
                Auth::attempt(array('name' => Input::get('userName'),
                    'password' => Input::get('lozinka')), $remember))
            return Redirect::route('home');
        Session::flash(self::DANGER_MESSAGE_KEY, 'Kriv unos!');
        return Redirect::route('signIn')
                        ->withInput();
    }

    public function logout()
	{
		Auth::logout();
		if(Session::has(self::DANGER_MESSAGE_KEY))
			Session::flash(self::DANGER_MESSAGE_KEY, Session::get(self::DANGER_MESSAGE_KEY));
		return Redirect::route('signIn');
	}

	public function changePassword($id)
	{
		$djelatnik = User::find($id);
		if(!$djelatnik)
			return $this->itemNotFound();
		return View::make('Djelatnik.changePassword')
		->with('instruktor', $djelatnik);
	}

	public function postChangePassword($id)
	{
		$djelatnik = User::find($id);
		if(!$djelatnik)
			return $this->itemNotFound();
		if(!(Input::has('oldpass')||Auth::user()->hasPermission(Permission::PERMISSION_PASSWORD_RESET))
                        ||!Input::has('newpass')||!Input::has('rep'))
			return Redirect::route('Djelatnik.changePassword', $id)
		->with(self::DANGER_MESSAGE_KEY, 'Nedovoljan unos!');
		$oldpass = Input::get('oldpass');
		$newpass = Input::get('newpass');
		$rep = Input::get('rep');
		if($newpass != $rep||!(Auth::user()->hasPermission(Permission::PERMISSION_PASSWORD_RESET)||
                        Hash::check($oldpass, $djelatnik->lozinka)))
			return Redirect::route('Djelatnik.changePassword', $id)
		->with(self::DANGER_MESSAGE_KEY, 'Kriv unos!');
		$djelatnik->lozinka = Hash::make($newpass);
		$djelatnik->save();
		return Redirect::route('Djelatnik.show', $id)
		->with(self::SUCCESS_MESSAGE_KEY, 'Uspješna promjena zaporke!');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return View::make('Djelatnik.index')
		->with('list', $this->_list());
	}

	public function _list($page = 1, $searchString = null) {
        $djelatnici = User::with('role', 'role.permissions');
        if (!empty($searchString)) {
            $djelatnici = $djelatnici->where('name', 'like', '%' . $searchString . '%');
        }
        $djelatnici = $djelatnici->orderBy('name');
        if ($page != 1)
            Paginator::setCurrentPage($page);
        $djelatnici = $djelatnici->paginate(10);
        $v = View::make('Djelatnik.list')
                ->with('instruktori', $djelatnici);
        if (Request::ajax())
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
		return View::make('Djelatnik.create');
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make(Input::all(),
			array(
				'name' => 'required|min:3',
				'lozinka' => 'required|min:5|same:ponovljena',
				'email' => 'email'));
		if($validator->fails())
		  	return Redirect::route('Djelatnik.create')
	  	->withInput()
	  	->with(self::DANGER_MESSAGE_KEY, $validator->messages()->first());

		$s = new User();
		$error = $s->getErrorOrSync(Input::all());
		if($error)
			return Redirect::route('Djelatnik.create')
			->withInput()
			->with(self::DANGER_MESSAGE_KEY, $error);

		return Redirect::route('Djelatnik.index')
	  	->with(self::SUCCESS_MESSAGE_KEY, 'Djelatnik je uspješno dodan.');
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id, $tjedan = null, $godina = null)
	{
		$djelatnik =  User::with('predmeti', 'role')->find($id);
		if(!$djelatnik)
			return $this->itemNotFound();
		return View::make('Djelatnik.show')
		->with('instruktor',$djelatnik)
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
		$djelatnik =  User::find($id);
		if(!$djelatnik)
			return $this->itemNotFound();
		return View::make('Djelatnik.create')
		->with('instruktor', $djelatnik);
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$djelatnik = User::find($id);
		if(!$djelatnik)
			return $this->itemNotFound();

		$error = $djelatnik->getErrorOrSync(Input::all());
		if($error)
			return Redirect::route('Djelatnik.edit')
			->withInput()
			->with(self::DANGER_MESSAGE_KEY, $error);

		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Djelatnik uspješno uređen.');
		return Redirect::route('Djelatnik.show', $id);
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$djelatnik = User::find($id);
		if(!$djelatnik)
			return $this->itemNotFound();
		elseif($djelatnik->id == Auth::id())
			Session::flash(self::DANGER_MESSAGE_KEY, 'Ne možeš ukloniti samoga sebe.');
		else{
			$djelatnik->delete();
			Session::flash(self::SUCCESS_MESSAGE_KEY, 'Djelatnik je uspješno uklonjen!');
		}
		return Redirect::route('Djelatnik.index');
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
		return View::make('Djelatnik.raspored')
		->with('tjedan', $tjedan)
		->with('godina', $godina)
		->with('instruktor', User::find($id))
		->with('strana_rasporeda', \Helpers\Raspored::RasporedForUserInWeek($id, $tjedan, $godina));
	}
}