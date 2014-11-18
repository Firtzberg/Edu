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
        const JSON_KATEGORIJE_IDENTIFIER = 'kategorije';
        const JSON_PREDMETI_IDENTIFIER = 'predmeti';
        const JSON_SELECTED_KATEGORIJA_IDENTIFIER = 'kategorija';
        const JSON_SELECTED_PREDMET_IDENTIFIER = 'predmet';
        const JSON_SELECTED_IDENTIFIER = 'selected';
        const JSON_CONTENT_IDENTIFIER = 'content';
        const JSON_TYPE_IDENTIFIER = 'type';
        const JSON_ID_IDENTIFIER = 'id';

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
        
        /**
     * Checks if there are any child Predmeti or Kategorije for the user
         * with id equal to $djelatnik_id
     * @param int $djelatnik_id Id of the user
     * @return boolean
     */
    private function hasChildrenFor($djelatnik_id) {
        if (Predmet::select('id', 'ime')
                        ->where('kategorija_id', '=', $this->id)
                        ->withUser($djelatnik_id)
                        ->count() > 0) {
            return true;
        }
        $kategorije = Kategorija
                ::where('nadkategorija_id', '=', $this->id)
                ->whereRaw('nadkategorija_id != id')
                ->get();
        foreach ($kategorije as $kategorija){
            if ($kategorija->hasChildrenFor($djelatnik_id)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Gets all child Predmeti allowed for the user with id equal to $djelatnik_id
     * @param int $djelatnik_id Id of User
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getChildrenPremetiFor($djelatnik_id) {
        return Predmet::select('id', 'ime')
                        ->where('kategorija_id', '=', $this->id)
                        ->withUser($djelatnik_id)
                        ->get();
    }

    /**
     * Gets all cild Kategorije which have a nested Predmeti for the user with id equal to $djelatnik_id
     * @param int $djelatnik_id Id of User
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getChildrenKategorijeFor($djelatnik_id) {
        return Kategorija::select('id', 'ime')
                        ->where('nadkategorija_id', '=', $this->id)
                        ->whereRaw('nadkategorija_id != id')
                        ->get()
                        ->filter(function($kategorija)use($djelatnik_id) {
                            return $kategorija->hasChildrenFor($djelatnik_id);
                        });
    }

    /**
     * Gets an array for making a dropdown in Rezervacija.create view using selectManager.
     * @param int $djelatnik_id Id of USer
     * @return array Array with 'predmeti' and 'kategorije' keys
     */
    public function getChildrenFor($djelatnik_id) {
        return array(self::JSON_KATEGORIJE_IDENTIFIER => array_values($this->getChildrenKategorijeFor($djelatnik_id)->toArray()),
            self::JSON_PREDMETI_IDENTIFIER => array_values($this->getChildrenPremetiFor($djelatnik_id)->toArray()));
    }
    /**
     * Gets ComplexDataStructure for initializing selectManager, and responding to Ajax
     * @param int $djelatnik_id Id of User
     * @return array
     */
    public function getHierarchyFor($djelatnik_id = null) {
        if (!$djelatnik_id) {
            return array();
        }
        $kategorije = $this->getChildrenKategorijeFor($djelatnik_id);
        $predmeti = $this->getChildrenPremetiFor($djelatnik_id);
        if(count($kategorije) + count($predmeti) == 1){
            if(count($predmeti) == 1){
                return array(
                    array(
                        self::JSON_CONTENT_IDENTIFIER => array(self::JSON_PREDMETI_IDENTIFIER => array_values($predmeti->toArray())),
                        self::JSON_SELECTED_IDENTIFIER => array(
                            self::JSON_TYPE_IDENTIFIER => self::JSON_SELECTED_PREDMET_IDENTIFIER,
                            self::JSON_ID_IDENTIFIER => $predmeti[0]->id
                        )
                    )
                );
            }
            return array_merge(array(
                array(
                    self::JSON_CONTENT_IDENTIFIER => array(self::JSON_KATEGORIJE_IDENTIFIER => array_values($kategorije->toArray())),
                    self::JSON_SELECTED_IDENTIFIER => array(
                        self::JSON_TYPE_IDENTIFIER => self::JSON_SELECTED_KATEGORIJA_IDENTIFIER,
                        self::JSON_ID_IDENTIFIER => $kategorije[0]->id
                    )
                )
                    ), $kategorije[0]->getHierarchyFor($djelatnik_id));
        }
        return array(
            array(
                self::JSON_CONTENT_IDENTIFIER => array(
                    self::JSON_KATEGORIJE_IDENTIFIER => array_values($kategorije->toArray()),
                    self::JSON_PREDMETI_IDENTIFIER => array_values($predmeti->toArray())
                )
            )
        );
    }

    /**
         * 
         * @return array
         */
	public function path(){
		$nadkategorija = $this->nadkategorija()->first();
		if ($nadkategorija->id == $this->id) {
            $path = array();
        } else {
            $path = $this->nadkategorija->path();
        }
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