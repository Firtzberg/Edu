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

}
