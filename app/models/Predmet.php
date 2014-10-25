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