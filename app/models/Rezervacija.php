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

}
