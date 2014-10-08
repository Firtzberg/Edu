<?php

class Predmet extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'predmeti';
	
	protected $guarded = array('id');

	public function nadkategorija()
	{
		return $this->belongsTo('Kategorija','kategorija_id');
	}

	public function cijene(){
		return $this->belongsToMany('Mjera', 'cijene')
		->withPivot('individualno', 'popust', 'minimalno')
		->withTimestamps();
	}
}