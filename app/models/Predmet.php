<?php

class Predmet extends Eloquent {
	const NOT_FOUND_MESSAGE = 'Zadani predmet nije pronađen u sustavu.';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'predmeti';
	
	protected $guarded = array('id');

	public function kategorija()
	{
		return $this->belongsTo('Kategorija','kategorija_id');
	}

	public function cijene(){
		return $this->belongsToMany('Mjera', 'cijene')
		->withPivot('individualno', 'popust', 'minimalno')
		->withTimestamps();
	}
}