<?php

class Naplata extends Eloquent {

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

}
