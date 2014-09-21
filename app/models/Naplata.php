<?php

class Naplata extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'naplate';

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

	public function rezervacija()
	{
		return $this->belongsTo('Rezervacija','rezervacija_id');
	}

}
