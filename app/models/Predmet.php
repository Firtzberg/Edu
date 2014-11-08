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

	public function cijene(){
		return $this->belongsToMany('Mjera', 'cijene')
		->withPivot('individualno', 'popust', 'minimalno')
		->withTimestamps();
	}

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
		$this->cijene()->sync($mjereSyncronizator);
            
        if(count($user_ids) > 0)
            $this->users()->sync($user_ids);
        else $this->users()->detach();
	}

	public function getErrorOrCijenaSyncArray($input){
		$mjere = Mjera::all();
		$syncArray = array();

		//obilazak za svaku mjeru u sustavu
		foreach ($mjere as $mjera) {
			$cijena = array();

			//začimanje potrebnih podataka
			if(isset($input['individualno-cijena-'.$mjera->id]))
				$individualno = $input['individualno-cijena-'.$mjera->id];
			else $individualno = 0;

			if(isset($input['popust-cijena-'.$mjera->id]))
				$popust = $input['popust-cijena-'.$mjera->id];
			else $popust = 0;

			if(isset($input['minimalno-cijena-'.$mjera->id]))
				$minimalno = $input['minimalno-cijena-'.$mjera->id];
			else $minimalno = 0;
			//kraj začimanja potrebnih podataka

			//provjera vrijednosti podataka
			if($individualno < 0)
				return 'Individualna cijena za '.$mjera->znacenje.' ne može biti negativna.';

			if($popust < 0)
				return 'Popust po dodatnoj osobi za '.$mjera->znacenje.' ne može biti negativan.';

			if($minimalno < 0)
				return 'Minimalna cijena za '.$mjera->znacenje.' ne može biti negativan.';
			if($minimalno > $individualno)
				return 'Minimalna cijena za '.$mjera->znacenje.' ne može biti manja od individualne cijene.';
			//kraj provjere vrijednosti podataka

			//pridruživanje vrijednosti
			$syncArray[$mjera->id] = array(
				'individualno' => $individualno,
				'popust' => $popust,
				'minimalno' => $minimalno
			);
			//kraj pridruživanja
		}
		//kraj obilaska za svaku mjeru u sustavu
		return $syncArray;
	}

	public function link(){
		return link_to_route('Predmet.show', $this->ime, array('id' => $this->id));
	}
}