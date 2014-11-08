<?php

/**
 * Naplata
 *
 * @property integer $rezervacija_id
 * @property integer $ukupno_uplaceno
 * @property integer $za_instruktora
 * @property integer $za_tvrtku
 * @property integer $stvarna_kolicina
 * @property integer $stvarna_mjera
 * @property string $napomena
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Rezervacija $rezervacija
 * @property-read \Mjera $stvarnaMjera
 * @method static \Illuminate\Database\Query\Builder|\Naplata whereRezervacijaId($value) 
 * @method static \Illuminate\Database\Query\Builder|\Naplata whereUkupnoUplaceno($value) 
 * @method static \Illuminate\Database\Query\Builder|\Naplata whereZaInstruktora($value) 
 * @method static \Illuminate\Database\Query\Builder|\Naplata whereZaTvrtku($value) 
 * @method static \Illuminate\Database\Query\Builder|\Naplata whereStvarnaKolicina($value) 
 * @method static \Illuminate\Database\Query\Builder|\Naplata whereStvarnaMjera($value) 
 * @method static \Illuminate\Database\Query\Builder|\Naplata whereNapomena($value) 
 * @method static \Illuminate\Database\Query\Builder|\Naplata whereCreatedAt($value) 
 * @method static \Illuminate\Database\Query\Builder|\Naplata whereUpdatedAt($value) 
 */
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
