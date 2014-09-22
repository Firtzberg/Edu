<?php

class UcionicaController extends \BaseController {

	private $startHour = 8;
	private $endHour = 22;
	protected $layout = 'layouts.master';

	public function __construct()
    {
    	$this->beforeFilter('admin', array('only' =>
    		array('create', 'store', 'edit', 'update', 'destroy')));
    }
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$this->layout->title = "Popis učionica";
		$this->layout->content =
		View::make('Ucionica.index')
		->with('ucionice', Ucionica::paginate(10));
		return $this->layout;
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$this->layout->title = "Dodaj ucionicu";
		$this->layout->content =
		View::make('Ucionica.create');
		return $this->layout;
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
			Session::flash('poruka', 'Učionica je uspješno dodana.');
	  		return Redirect::action('UcionicaController@index');
	  	}
	  	return Redirect::action('UcionicaController@create')
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
		$u = Ucionica::find($id);
		$this->layout->title = $u->naziv." - Učionica";
		$this->layout->content =
		View::make('Ucionica.show')
		->with('ucionica', $u)
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
		$u = Ucionica::find($id);
		$this->layout->title = $u->naziv." - Uredi učionicu";
		$this->layout->content =
		View::make('Ucionica.create')
		->with('ucionica', $u);
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
		$u = Ucionica::find($id);
		$u->update(Input::all());
		if(true)
		{
			Session::flash('poruka','Učionica uspješno uređena');
			return Redirect::action('UcionicaController@show', array($id));
		}

		return Redirect('ucionica.edit')
		->withInput()
		->withErrors($u->errors());
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Ucionica::find($id)->delete();
		Session::flash('poruka', 'Učionica je uspješno uklonjena!');
		return Redirect::action('UcionicaController@index');
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
			if(empty($r->predmet))
				$r->predmet = "Nema predmeta";
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
			for($i = $this->startHour*4; $i < ($this->endHour+1)*4;)
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

		return View::make('Ucionica.raspored')
		->with('ucionica', Ucionica::find($id))
		->with('tjedan', $tjedan)
		->with('godina', $godina)
		->with('grid', $grid)
		->with('startHour', $this->startHour)
		->with('endHour', $this->endHour);
	}

	private function getRezervacije($week, $year, $id)
	{
		$time = new DateTime();
		$time->setTime(0, 0);
		$time->setISODate($year, $week);
	    $min = $time->format('Y-m-d H:i:s');
	    $max = $time->modify('+1 week')->format('Y-m-d H:i:s');
	    return Rezervacija::with('mjera')
	    ->where('ucionica_id', '=', $id)
	    ->whereBetween('pocetak_rada', array($min, $max))
	    ->with('instruktor')
	    ->get();
	}
}