<?php

class Ucionica extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'ucionice';

	protected $guarded = array('id');

	public function rezervacije()
	{
		return $this->hasMany('Rezervacija');
	}

}
