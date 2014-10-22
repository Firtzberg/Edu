<?php

class InstruktorController extends \BaseController {

	protected $layout = "layouts.master";

	public function __construct()
    {
    	$this->beforeFilter('admin', array('only' =>
    		array('create', 'store', 'destroy')));
    	$this->beforeFilter('myProfile', array('only' =>
    		array('changePassword', 'postChangePassword', 'edit', 'update')));
    }

    private function itemNotFound(){
		Session::flash(self::DANGER_MESSAGE_KEY, User::NOT_FOUND_MESSAGE);
  		return Redirect::route('Instruktor.index');
    }

	public function signIn()
	{
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
		->with(self::SUCCESS_MESSAGE_KEY, 'Kriv unos!');
	}

	public function logout()
	{
		Auth::logout();
		if(Session::has(self::SUCCESS_MESSAGE_KEY))
			Session::flash(self::SUCCESS_MESSAGE_KEY, Session::get(self::SUCCESS_MESSAGE_KEY));
		return Redirect::route('signIn');
	}

	public function changePassword($id)
	{
		$this->layout->title = "Promjena zaporke";
		$this->layout->content =
		View::make('Instruktor.changePassword')
		->with('instruktor', User::find($id));
		return $this->layout;
	}

	public function postChangePassword($id)
	{
		if(!(Input::has('oldpass')||Auth::user()->is_admin)||!Input::has('newpass')||!Input::has('rep'))
			return Redirect::route('Instruktor.changePassword', $id)
		->with(self::SUCCESS_MESSAGE_KEY, 'Nedovoljan unos!');
		$oldpass = Input::get('oldpass');
		$newpass = Input::get('newpass');
		$rep = Input::get('rep');
		$u = User::find($id);
		if($newpass != $rep||!(Hash::check($oldpass, $u->lozinka)||Auth::user()->is_admin))
			return Redirect::route('Instruktor.changePassword', $id)
		->with(self::SUCCESS_MESSAGE_KEY, 'Kriv unos!');
		$u->lozinka = Hash::make($newpass);
		$u->save();
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
		$this->layout->title = "Popis Instruktora";
		$this->layout->content = View::make('Instruktor.index')
		->with('list', $this->_list());
		return $this->layout;
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
		$this->layout->title = "Dodaj instruktora";
		$this->layout->content =
		View::make('Instruktor.create');
		return $this->layout;
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
				'name' => 'required|min:3|unique:users',
				'lozinka' => 'required|min:5|same:ponovljena',
				'email' => 'email'));
		if($validator->fails())
		  	return Redirect::route('Instruktor.create')
	  	->withInput()
	  	->with(self::SUCCESS_MESSAGE_KEY, $validator->messages()->first());

		$s = new User();
		$s->name = Input::get('name');
		$s->broj_mobitela = Input::get('broj_mobitela');
		$s->email = Input::get('email');
		$s->lozinka = Hash::make(Input::get('lozinka'));
		$s->save();
		return Redirect::route('Instruktor.index')
	  	->with('flash', 'Instruktor je uspješno dodan.');
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id, $tjedan = null, $godina = null)
	{
		$i =  User::find($id);
		if(!$i)
			return $this->itemNotFound();
		$this->layout->title = $i->name." - Instruktor";
		$this->layout->content =
		View::make('Instruktor.show')
		->with('instruktor',$i)
		->with('raspored', $this->raspored($id, $tjedan, $godina));
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
		$i =  User::find($id);
		if(!$i)
			return $this->itemNotFound();
		$this->layout->title = $i->name." - Uredi instruktora";
		$this->layout->content =
		View::make('Instruktor.create')
		->with('instruktor', $i);
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
		$i = User::find($id);
		if(!$i)
			return $this->itemNotFound();
		$i->email = Input::get('email');
		$i->broj_mobitela= Input::get('broj_mobitela');
		if(Input::has('name'))
			$i->name = Input::get('name');
		$i->save();
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
		$u = User::find($id);
		if(!$u)
			return $this->itemNotFound();
		elseif($u->is_admin)
			Session::flash(self::DANGER_MESSAGE_KEY, 'Nije moguće ukloniti administratora.');
		else{
			$u->delete();
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