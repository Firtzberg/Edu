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
		return $this->hasMany('Kategorija', 'nadkategorija_id')
		->whereRaw('id != nadkategorija_id')
		->orderBy('ime');
	}

	public function predmeti()
	{
		return $this->hasMany('Predmet')
		->orderBy('ime');
	}

	public function getBreadCrumbs()
	{
		$nadkategorija = $this->nadkategorija;
		$myLink = link_to_route('Kategorija.show', $this->ime, array('id' => $this->id));
		if($nadkategorija->id == $this->id)
			return $myLink;
		return $nadkategorija->getBreadCrumbs().'/'.$myLink;
	}
}