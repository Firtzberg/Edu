<?php








class InstruktorController extends \ResourceController {
    
    public function __construct() {
        parent::__construct();

        $this->requireDeletePermission(Permission::PERMISSION_REMOVE_USER);
        $this->beforeFilter(function() {
            if (!Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_USER)) {
                return Redirect::to('logout');
            }
        }, array('only' => array('create', 'store')));

        $this->beforeFilter('myProfile', array('only' =>
            array('changePassword', 'postChangePassword', 'edit', 'update')));

        $this->beforeFilter(function($route) {
            if (!(Auth::user()->hasPermission(Permission::PERMISSION_PASSWORD_RESET) || Auth::id() == $route->getParameter('User'))) {
                return Redirect::to('logout');
            }
        }, array('only' => array('changePassword', 'postChangePassword')));
    }

    private function itemNotFound() {
        Session::flash(self::DANGER_MESSAGE_KEY, User::NOT_FOUND_MESSAGE);
        return Redirect::route('Instruktor.index');
    }

    public function signIn()
	{
		if(Auth::check())
			return Redirect::route('Instruktor.show', Auth::id());
		return View::make('signIn');
	}

	public function login()
	{
		if(Auth::check())
			return Redirect::route('Instruktor.show', Auth::id())
		->with(self::SUCCESS_MESSAGE_KEY, 'Već si bio registriran');
		$remember = Input::get('remember');

		if(Input::has('userName')&&Input::has('lozinka')&&
			Auth::attempt(array('name' => Input::get('userName'),
			'password' => Input::get('lozinka')), $remember))
			return Redirect::route('Instruktor.show', Auth::id());
		return Redirect::route('signIn')
		->withInput()
		->with(self::DANGER_MESSAGE_KEY, 'Kriv unos!');
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
		$instruktor = User::find($id);
		if(!$instruktor)
			return $this->itemNotFound();
		return View::make('Instruktor.changePassword')
		->with('instruktor', $instruktor);
	}

	public function postChangePassword($id)
	{
		$instruktor = User::find($id);
		if(!$instruktor)
			return $this->itemNotFound();
		if(!(Input::has('oldpass')||Auth::user()->is_admin)||!Input::has('newpass')||!Input::has('rep'))
			return Redirect::route('Instruktor.changePassword', $id)
		->with(self::DANGER_MESSAGE_KEY, 'Nedovoljan unos!');
		$oldpass = Input::get('oldpass');
		$newpass = Input::get('newpass');
		$rep = Input::get('rep');
		if($newpass != $rep||!(Hash::check($oldpass, $instruktor->lozinka)||Auth::user()->is_admin))
			return Redirect::route('Instruktor.changePassword', $id)
		->with(self::DANGER_MESSAGE_KEY, 'Kriv unos!');
		$instruktor->lozinka = Hash::make($newpass);
		$instruktor->save();
		return Redirect::route('Instruktor.show', $id)
		->with(self::SUCCESS_MESSAGE_KEY, 'Uspješna promjena zaporke!');
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return View::make('Instruktor.index')
		->with('list', $this->_list());
	}

	public function _list($page = 1, $searchString = null){
		if(!empty($searchString)){
			$instruktori = User::where('name', 'like', '%'.$searchString.'%')
			->orderBy('name');
		}
		else
			$instruktori = User::orderBy('name');
		if($page != 1)
			Paginator::setCurrentPage($page);
		$instruktori = $instruktori->paginate(10);
		$v = View::make('Instruktor.list')
		->with('instruktori', $instruktori);
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
		return View::make('Instruktor.create');
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
		  	return Redirect::route('Instruktor.create')
	  	->withInput()
	  	->with(self::DANGER_MESSAGE_KEY, $validator->messages()->first());

		$s = new User();
		$error = $s->getErrorOrSync(Input::all());
		if($error)
			return Redirect::route('Instruktor.create')
			->withInput()
			->with(self::DANGER_MESSAGE_KEY, $error);

		return Redirect::route('Instruktor.index')
	  	->with(self::SUCCESS_MESSAGE_KEY, 'Instruktor je uspješno dodan.');
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id, $tjedan = null, $godina = null)
	{
		$instruktor =  User::with('predmeti', 'role')->find($id);
		if(!$instruktor)
			return $this->itemNotFound();
		return View::make('Instruktor.show')
		->with('instruktor',$instruktor)
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
		$instruktor =  User::find($id);
		if(!$instruktor)
			return $this->itemNotFound();
		return View::make('Instruktor.create')
		->with('instruktor', $instruktor);
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$instruktor = User::find($id);
		if(!$instruktor)
			return $this->itemNotFound();

		$error = $instruktor->getErrorOrSync(Input::all());
		if($error)
			return Redirect::route('Instruktor.create')
			->withInput()
			->with(self::DANGER_MESSAGE_KEY, $error);

		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Instruktor uspješno uređen');
		return Redirect::route('Instruktor.show', $id);
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$instruktor = User::find($id);
		if(!$instruktor)
			return $this->itemNotFound();
		elseif($instruktor->is_admin)
			Session::flash(self::DANGER_MESSAGE_KEY, 'Nije moguće ukloniti administratora.');
		else{
			$instruktor->delete();
			Session::flash(self::SUCCESS_MESSAGE_KEY, 'Instruktor je uspješno uklonjen!');
		}
		return Redirect::route('Instruktor.index');
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
		return View::make('Instruktor.raspored')
		->with('tjedan', $tjedan)
		->with('godina', $godina)
		->with('instruktor', User::find($id))
		->with('strana_rasporeda', $this->strana_rasporeda($id, $tjedan, $godina));
	}

	public function strana_rasporeda($id, $tjedan, $godina){
		$t = new DateTime();
		$t->setISODate($godina, $tjedan);
		$grid = array();
		$dani = array(1 => 'Ponedjeljak', 'Utorak', 'Srijeda', 'Četvrtak', 'Petak', 'Subota', 'Nedjelja');
		for($i = 1; $i < 8; $i++)
		{
			$dani[$i] = $dani[$i].$t->format(' j.n.');
			$t->add(new DateInterval('P1D'));
			$grid[$dani[$i]] = array();
		}

		$rezervacije = $this->getRezervacije($tjedan, $godina, $id);
		foreach ($rezervacije as $r) {
			$pocetak = strtotime($r->pocetak_rada);
			$kraj = strtotime($r->kraj_rada());
			$d = $dani[date('N',$pocetak)];
			$key =date('G:i', $pocetak);
			$grid[$d][$key] = array('span' => (int)(($kraj-$pocetak)/60/15),
				'rezervacija' => $r,
				'ucionica' =>  $r->ucionica);
		}

		foreach ($dani as $d) {
			$col = $grid[$d];
			for($i = self::START_HOUR*4; $i < (self::END_HOUR+1)*4;)
			{
				if($i%4==0)
					$key = ((int)($i/4)).':00';
				else $key = ((int)($i/4)).':'.($i%4*15);
				if(isset($col[$key]))
				{
					$i += $col[$key]['span'];
				}
				else
				{
					$j=$i;
					do{
						$j++;
						if($j%4==0)
							$nkey = ((int)($j/4)).':00';
						else $nkey = ((int)($j/4)).':'.($j%4*15);
					}while($j%4!=0 && !isset($col[$nkey]));
					$grid[$d][$key] = array('span' => $j-$i);
					$i=$j;
				}
			}
		}

		return View::make('Instruktor.strana_rasporeda')
		->with('instruktor', User::find($id))
		->with('grid', $grid);
	}

	private function getRezervacije($week, $year, $id)
	{
		$time = new DateTime();
		$time->setTime(0, 0);
		$time->setISODate($year, $week);
	    $min = $time->format('Y-m-d H:i:s');
	    $max = $time->modify('+1 week')->format('Y-m-d H:i:s');
	    return Rezervacija::with('mjera')
	    ->where('instruktor_id', '=', $id)
	    ->whereBetween('pocetak_rada', array($min, $max)) 
	    ->with('ucionica')
	    ->get();
	}

}