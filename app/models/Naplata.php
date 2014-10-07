<?php

class Naplata extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'naplate';

	protected $guarded = array('id');

	public function rezervacija()
	{
		return $this->belongsTo('Rezervacija','rezervacija_id');
	}

}
