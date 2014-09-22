<?php

class Rezervacija extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'rezervacije';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $guarded = array('id');

	public function naziv()
	{
		return $this->naziv;
	}

	public function instruktor()
	{
		return $this->belongsTo('User','instruktor_id');
	}

	public function ucionica()
	{
		return $this->belongsTo('Ucionica','ucionica_id');
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

}
