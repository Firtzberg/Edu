<?php

/**
 * Predmet
 *
 * @property integer $id
 * @property string $ime
 * @property integer $kategorija_id
 * @property boolean $enabled
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Kategorija $kategorija
 * @property-read \Illuminate\Database\Eloquent\Collection|\User[] $users
 * @property-read \Illuminate\Database\Eloquent\Collection|\Mjera[] $cijene
 * @method static \Illuminate\Database\Query\Builder|\Predmet whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Predmet whereIme($value)
 * @method static \Illuminate\Database\Query\Builder|\Predmet whereKategorijaId($value)
 * @method static \Illuminate\Database\Query\Builder|\Predmet whereEnabled($value)
 * @method static \Illuminate\Database\Query\Builder|\Predmet whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Predmet whereUpdatedAt($value)
 * @method static \Predmet withUser($user_id) 
 */


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

	public function users()
	{
		return $this->belongsToMany('User','predmet_user');
	}

    public function c_m_p() {
        return $this->belongsToMany('Mjera', 'c_m_p')
                        ->withPivot('cjenovnik_id');
    }

    public function cjenovnik($mjera_id) {
        $mjera = $this->c_m_p->first(function($index, $mjera) use ($mjera_id) {
            return $mjera->id == $mjera_id;
        });
        if (!$mjera) {
            return null;
        }
        return Cjenovnik::find($mjera->pivot->cjenovnik_id);
    }

    public function cijene(){
		return $this->belongsToMany('Mjera', 'cijene')
		->withPivot('individualno', 'popust', 'minimalno')
		->withTimestamps();
	}
        
        /**
     * 
     * @param \Illuminate\Database\Query\Builder $query
     * @param int $user_id
         * @return \Illuminate\Database\Query\Builder
     */
    public function scopeWithUser($query, $user_id) {
        return $query->whereExists(function($query) use ($user_id) {
                    $query->select(DB::Raw('1'))
                            ->from('predmet_user')
                            ->whereRaw('predmet_user.predmet_id = predmeti.id')
                            ->where('predmet_user.user_id', '=', $user_id);
                });
    }

    /**
         * 
         * @param array $input
         * @return null|string
         */
	public function getErrorOrSync($input){
		$ime = $this->ime;
		if(isset($input['ime']))
			$ime = $input['ime'];
		if(!$ime)
			return 'Ime predmeta je obvezno.';

		$kategorija_id = $this->kategorija_id;
		if(isset($input['kategorija_id']))
			$kategorija_id = $input['kategorija_id'];
		if(!$kategorija_id)
			return 'Nije zadana kategorija predmeta.';

		$kategorija = Kategorija::find($kategorija_id);
		if(!$kategorija)
			return Kategorija::NOT_FOUND_MESSAGE;

        //provjera zauzetosti imena
        $query = $kategorija->predmeti()->where('ime', '=', $ime);
        if($this->id > 0)
            $query = $query->where('id', '!=', $this->id);
        if($query->count() > 0)
            return 'U kategoriji '.$kategorija->ime.' već postoji predmet s imenom '.$ime.'.';
        //kraj provjere zauzetosti imena

		$mjereSyncronizator = $this->getErrorOrCijenaSyncArray($input);
		if(!is_array($mjereSyncronizator))
			return $mjereSyncronizator;

        if(isset($input['allowed']))
            $user_ids = $input['allowed'];
        else $user_ids = array();
        if(!$user_ids || !is_array($user_ids))
            $user_ids = array();

        if(count($user_ids) > 0)
            $user_ids = User::select('id')
            ->whereIn('id', $user_ids)
            ->get()
            ->lists('id');

		$this->ime = $ime;

		$kategorija->predmeti()->save($this);
		$this->c_m_p()->sync($mjereSyncronizator);
            
        if(count($user_ids) > 0)
            $this->users()->sync($user_ids);
        else $this->users()->detach();
	}

	public function getErrorOrCijenaSyncArray($input){
		$syncArray = array();

		//obilazak za svaku mjeru u sustavu
		foreach (Mjera::all() as $mjera) {
			//začimanje potrebnih podataka
			if(isset($input["cjenovnik_id_$mjera->id"]))
				$cjenovnik_id = $input["cjenovnik_id_$mjera->id"];
			else return 'Niste odabrali cjenovnik za '.$mjera->znacenje.'.';
			//kraj začimanja potrebnih podataka

			//provjera vrijednosti podataka
                            $cjenovnik = Cjenovnik::find($cjenovnik_id);
                            if(!$cjenovnik)
                                    return "Zadani cjenovnik za $mjera->znacenje nije pronađen u sustavu.";
			//kraj provjere vrijednosti podataka

			//pridruživanje vrijednosti
			$syncArray[$mjera->id] = array(
				'cjenovnik_id' => $cjenovnik_id
			);
			//kraj pridruživanja
		}
		//kraj obilaska za svaku mjeru u sustavu
		return $syncArray;
	}

        /**
         * 
         * @return string
         */
	public function link(){
            if (Auth::user()->hasPermission(Permission::PERMISSION_VIEW_PREDMET_KATEGORIJA)) {
            return link_to_route('Predmet.show', $this->ime, array('id' => $this->id));
        }
        return $this->ime;
	}
}