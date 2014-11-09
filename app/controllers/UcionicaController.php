<?php









class UcionicaController extends \ResourceController {
    
    public function __construct() {
        parent::__construct();
        
        $this->requireManagePermission(Permission::PERMISSION_MANAGE_UCIONICA);
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
	public function store()
	{
		$s = Ucionica::create(Input::all());
		$s->save();
		if(true)
		{
			Session::flash(self::SUCCESS_MESSAGE_KEY, 'Učionica je uspješno dodana.');
	  		return Redirect::route('Ucionica.index');
	  	}
	  	return Redirect::route('Ucionica.create')
	  	->withInput()
	  	->withErrors($s->errors());
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
	public function update($id)
	{
		$ucionica = Ucionica::find($id);
		if(!$ucionica)
			return $this->itemNotFound();
		$ucionica->update(Input::all());
		if(true)
		{
			Session::flash(self::SUCCESS_MESSAGE_KEY,'Učionica uspješno uređena');
			return Redirect::route('Ucionica.show', array($id));
		}

		return Redirect('ucionica.edit')
		->withInput()
		->withErrors($ucionica->errors());
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
		->with('strana_rasporeda', $this->strana_rasporeda($id, $tjedan, $godina));
	}

	private function strana_rasporeda($id, $tjedan, $godina){
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
				'instruktor' =>  $r->instruktor);
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

		return View::make('Ucionica.strana_rasporeda')
		->with('ucionica', Ucionica::find($id))
		->with('grid', $grid);
	}

	private function getRezervacije($week, $year, $id)
	{
		$time = new DateTime();
		$time->setTime(0, 0);
		$time->setISODate($year, $week);
	    $min = $time->format('Y-m-d H:i:s');
	    $max = $time->modify('+1 week')->format('Y-m-d H:i:s');
	    return Rezervacija::with('mjera', 'predmet')
	    ->where('ucionica_id', '=', $id)
	    ->whereBetween('pocetak_rada', array($min, $max))
	    ->with('instruktor')
	    ->get();
	}
}