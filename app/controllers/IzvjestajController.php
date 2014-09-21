<?php

class IzvjestajController extends BaseController {

	protected $layout = 'layouts.master';

	public function __construct()
    {
    	$this->beforeFilter('admin', array('only' =>
    		array('ukupni_tjedni_izvjestaj', 'ukupni_godisnji_izvjestaj')));
    	$this->beforeFilter('myProfile', array('only' =>
    		array('tjedni_izvjestaj', 'godisnji_izvjestaj')));
    }

	public function tjedni_izvjestaj($id, $tjedan = null, $godina = null)
	{
		return $this->t_izvjestaj($id, $tjedan, $godina);
	}

	public function ukupni_tjedni_izvjestaj($tjedan = null, $godina = null)
	{
		return $this->t_izvjestaj(null, $tjedan, $godina);
	}

	private function t_izvjestaj($id, $tjedan, $godina)
	{
		if(is_null($tjedan))
			$tjedan = date('W');
		if(is_null($godina))
			$godina = date('o');
		$dto = new DateTime();
		$dto = $dto->setISODate($godina, $tjedan);
		$tjedan = $dto->format('W');
		$godina = $dto->format('o');

		$primanja = Rezervacija::select(DB::raw('weekday(pocetak_rada)+1 as dan, '.
			'COALESCE(sum(timestampdiff(minute,pocetak_rada,kraj_rada)),0) as minuta, '.
			'COALESCE(sum(za_instruktora),0) as za_instruktora, '.
			'COALESCE(sum(za_tvrtku),0) as za_tvrtku, '.
			'COALESCE(sum(ukupno_uplaceno),0) as ukupno_uplaceno'))
		->leftJoin('naplate', 'naplate.rezervacija_id', '=', 'rezervacije.id');
		if(!is_null($id))
			$primanja = $primanja->where('instruktor_id', '=', $id);
		$primanja = $primanja->where(DB::raw('yearweek(pocetak_rada, 3)'), '=', $godina.$tjedan)
		->where(DB::raw('yearweek(kraj_rada, 3)'), '=', $godina.$tjedan)
		->groupBy(DB::raw('weekday(pocetak_rada)'))
		->get();
		$dani = array(1 => 'Ponedjeljak', 'Utorak', 'Srijeda', 'Četvrtak', 'Petak', 'Subota', 'Nedjelja');
		$di = new DateInterval('P1D');
		$di->invert = 1;
		$dto->add($di);
		$di->invert = 0;
		foreach ($dani as $key => $d) {
			$zarada[$key] = array(
				'datum' => $dto->add($di)->format('d. m.'),
				'dan' => $d,
				'sati' => 0,
				'za_instruktora' => 0,
				'za_tvrtku' => 0,
				'ukupno_uplaceno' => 0);
		}

		$minuta = 0;
		$za_instruktora = 0;
		$za_tvrtku = 0;
		$ukupno_uplaceno = 0;

		foreach ($primanja as $p) {
			$zarada[$p['dan']]['sati'] = $p['minuta']/60;
			$minuta += $p['minuta'];
			$zarada[$p['dan']]['za_instruktora'] = $p['za_instruktora'];
			$za_instruktora += $p['za_instruktora'];
			$zarada[$p['dan']]['za_tvrtku'] = $p['za_tvrtku'];
			$za_tvrtku += $p['za_tvrtku'];
			$zarada[$p['dan']]['ukupno_uplaceno'] = $p['ukupno_uplaceno'];
			$ukupno_uplaceno += $p['ukupno_uplaceno'];
		}

		$zarada[] = array(
			'datum' => null,
			'dan' => 'Ukupno',
			'sati' => $minuta/60,
			'za_instruktora' => $za_instruktora,
			'za_tvrtku' => $za_tvrtku,
			'ukupno_uplaceno' => $ukupno_uplaceno);

		$this->layout->content =
		View::make('Izvjestaj.tjedni_izvjestaj')
		->with('tjedan', $tjedan)
		->with('godina', $godina)
		->with('zarada', $zarada);
		if(is_null($id))
			$this->layout->title = "Tjedni izvještaj";
		else
		{
		$i = User::find($id);
			$this->layout->title = $i->name." - Tjedni izvještaj";
			$this->layout->content->with('instruktor', $i);
		}
		return $this->layout;
	}

	public function ukupni_godisnji_izvjestaj($godina = null)
	{
		return $this->g_izvjestaj(null, $godina);
	}

	public function godisnji_izvjestaj($id, $godina = null)
	{
		return $this->g_izvjestaj($id, $godina);
	}

	private function g_izvjestaj($id, $godina)
	{
		if(is_null($godina))
			$godina = date('Y');
		
		$primanja = Rezervacija::select(DB::raw('month(pocetak_rada) as mjesec, '.
			'COALESCE(sum(timestampdiff(minute,pocetak_rada,kraj_rada)),0) as minuta, '.
			'COALESCE(sum(za_instruktora),0) as za_instruktora, '.
			'COALESCE(sum(za_tvrtku),0) as za_tvrtku, '.
			'COALESCE(sum(ukupno_uplaceno),0) as ukupno_uplaceno'))
		->leftJoin('naplate', 'naplate.rezervacija_id', '=', 'rezervacije.id');
		if(!is_null($id))
			$primanja = $primanja->where('instruktor_id', '=', $id);
		$primanja = $primanja->where(DB::raw('year(pocetak_rada)'), '=', $godina)
		->where(DB::raw('year(kraj_rada)'), '=', $godina)
		->groupBy(DB::raw('month(pocetak_rada)'))
		->get();

		$mjeseci = array(1 => 'Siječanj', 'Veljača', 'Ožujak', 'Travanj', 'Svibanj', 'Lipanj',
			'Srpanj', 'Kolovoz', 'Rujan', 'Listopad', 'Studeni', 'Prosinac');
		foreach ($mjeseci as $m) {
			$zarada[$m] = array(
				'sati' => 0,
				'za_instruktora' => 0,
				'za_tvrtku' => 0,
				'ukupno_uplaceno' => 0);
		}
		$minuta = 0;
		$za_instruktora = 0;
		$za_tvrtku = 0;
		$ukupno_uplaceno = 0;
		foreach ($primanja as $p) {
			$zarada[$mjeseci[$p['mjesec']]]['sati'] = $p['minuta']/60;
			$minuta += $p['minuta'];
			$zarada[$mjeseci[$p['mjesec']]]['za_instruktora'] = $p['za_instruktora'];
			$za_instruktora += $p['za_instruktora'];
			$zarada[$mjeseci[$p['mjesec']]]['za_tvrtku'] = $p['za_tvrtku'];
			$za_tvrtku += $p['za_tvrtku'];
			$zarada[$mjeseci[$p['mjesec']]]['ukupno_uplaceno'] = $p['ukupno_uplaceno'];
			$ukupno_uplaceno += $p['ukupno_uplaceno'];
		}
		$zarada['Ukupno'] = array(
			'sati' => $minuta/60,
			'za_instruktora' => $za_instruktora,
			'za_tvrtku' => $za_tvrtku,
			'ukupno_uplaceno' => $ukupno_uplaceno);

		$this->layout->content =
		View::make('Izvjestaj.godisnji_izvjestaj')
		->with('godina', $godina)
		->with('zarada', $zarada);
		if(is_null($id))
			$this->layout->title = "Godišnji izvještaj";
		else
		{
		$i = User::find($id);
			$this->layout->title = $i->name." - Godišnji izvještaj";
			$this->layout->content->with('instruktor', $i);
		}
		return $this->layout;
	}
}