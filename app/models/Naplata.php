<?php

class Naplata extends Eloquent {
	const NOT_FOUND_MESSAGE = 'Zadana naplata nije pronađena u sustavu.';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'naplate';

	protected $primaryKey = 'rezervacija_id';

	protected $guarded = array('rezervacija_id');

	public function rezervacija()
	{
		return $this->belongsTo('Rezervacija','rezervacija_id');
	}

	public function stvarnaMjera(){
		return $this->belongsTo('Mjera', 'stvarna_mjera');
	}

	public function getSatnicaZaInstruktora($ukupno){
		$pravedni = floor($ukupno/30);
		$za_instruktora = $pravedni*20;
		$ukupno -= $pravedni * 30;
		if($ukupno < 10)
			$za_instruktora += $ukupno;
		elseif($ukupno < 20)
			$za_instruktora += 10;
		else $za_instruktora += $ukupno - 10;
		return $za_instruktora;
	}

}
