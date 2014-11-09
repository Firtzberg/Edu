<?php

/**
 * Kategorija
 *
 * @property integer $id
 * @property string $ime
 * @property integer $nadkategorija_id
 * @property boolean $enabled
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Kategorija $nadkategorija
 * @property-read \Illuminate\Database\Eloquent\Collection|\Kategorija[] $podkategorije
 * @property-read \Illuminate\Database\Eloquent\Collection|\Predmet[] $predmeti
 * @method static \Illuminate\Database\Query\Builder|\Kategorija whereId($value) 
 * @method static \Illuminate\Database\Query\Builder|\Kategorija whereIme($value) 
 * @method static \Illuminate\Database\Query\Builder|\Kategorija whereNadkategorijaId($value) 
 * @method static \Illuminate\Database\Query\Builder|\Kategorija whereEnabled($value) 
 * @method static \Illuminate\Database\Query\Builder|\Kategorija whereCreatedAt($value) 
 * @method static \Illuminate\Database\Query\Builder|\Kategorija whereUpdatedAt($value) 
 */

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
		$kategorije = Kategorija::select('id', 'ime')
		->where('nadkategorija_id', '=', $this->id)
		->whereRaw('nadkategorija_id != id')
		->get()
		->toArray();
		$predmeti = Predmet::select('id', 'ime')
		->where('kategorija_id', '=', $this->id)
		->get()
		->toArray();
		return array(
				'kategorije' => $kategorije,
				'predmeti' => $predmeti
			);
	}

        /**
         * 
         * @return array
         */
	public function path(){
		$nadkategorija = $this->nadkategorija()->first();
		if($nadkategorija->id == $this->id)
			$path = array();
		else $path = $this->nadkategorija->path();
		$path[] = $this;
		return $path;
	}

        /**
         * 
         * @return string
         */
	public function getBreadCrumbs()
	{
		$links = array_map(function($kategorija){
			return $kategorija->link();
		}, $this->path());
		return implode('/', $links);
	}

        /**
         * 
         * @return string
         */
	public function link(){
            if (Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_PREDMET_KATEGORIJA)) {
            return link_to_route('Kategorija.show', $this->ime, array('id' => $this->id));
        }
        return $this->ime;
    }
}