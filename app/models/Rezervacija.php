<?php

class Rezervacija extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'rezervacije';

	protected $guarded = array('id');

	public function instruktor()
	{
		return $this->belongsTo('User','instruktor_id');
	}

	public function ucionica()
	{
		return $this->belongsTo('Ucionica','ucionica_id');
	}

	public function predmet()
	{
		return $this->belongsTo('Predmet','predmet_id');
	}

	public function naplata()
	{
		return $this->hasOne('Naplata');
	}

	public function mjera()
	{
		return $this->belongsTo('Mjera','mjera_id');
	}

	public function kraj_rada()
	{
		$dt = new DateTime($this->pocetak_rada);
		$dt->add(new DateInterval('PT'.$this->mjera->trajanje*$this->kolicina.'M'));
		return $dt->format('Y-m-d H:i:s');
	}

	public function klijenti()
	{
		return $this->belongsToMany('Klijent', 'klijent_rezervacija')
		->withPivot('missed');
	}

}
