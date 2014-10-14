<?php

class Kategorija extends Eloquent {
	const NOT_FOUND_MESSAGE = 'Zadana kategorija nije pronađena u sustavu.';

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

	public function getEnabledChildren(){
		$data = array();
		$kategorije = Kategorija::select('id', 'ime')
		->where('nadkategorija_id', '=', $this->id)
		->where('enabled', '=', true)
		->whereRaw('nadkategorija_id != id')
		->get()
		->toArray();
		$predmeti = Predmet::select('id', 'ime')
		->where('kategorija_id', '=', $this->id)
		->where('enabled', '=', true)
		->get()
		->toArray();
		return array(
				'kategorije' => $kategorije,
				'predmeti' => $predmeti
			);
	}

	public function path(){
		$nadkategorija = $this->nadkategorija()->first();
		if($nadkategorija->id == $this->id)
			$path = array();
		else $path = $this->nadkategorija->path();
		$path[] = $this;
		return $path;
	}

	public function getBreadCrumbs()
	{
		$links = array_map(function($kategorija){
			return link_to_route('Kategorija.show', $kategorija->ime, array('id' => $kategorija->id));
		}, $this->path());
		return implode('/', $links);
	}
}