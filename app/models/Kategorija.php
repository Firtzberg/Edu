<?php

class Kategorija extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'kategorije';
	
	protected $guarded = array('id');

	public function nadkategorija()
	{
		return $this->belongsTo('Kategorija','nadkategorija_id');
	}

	public function podkategorije()
	{
		return $this->hasMany('Kategorija');
	}

	public function predmeti()
	{
		return $this->hasMany('Predmet');
	}
}