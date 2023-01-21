<?php

/**
 * Rezervacija
 *
 * @property integer $id
 * @property string $pocetak_rada
 * @property integer $mjera_id
 * @property integer $kolicina
 * @property integer $instruktor_id
 * @property integer $predmet_id
 * @property integer $ucionica_id
 * @property boolean $tecaj
 * @property integer $tecaj_broj_polaznika
 * @property string $napomena
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \User $instruktor
 * @property-read \Ucionica $ucionica
 * @property-read \Predmet $predmet
 * @property-read \Naplata $naplata
 * @property-read \Mjera $mjera
 * @property-read \Illuminate\Database\Eloquent\Collection|\Klijent[] $klijenti
 * @method static \Illuminate\Database\Query\Builder|\Rezervacija whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Rezervacija wherePocetakRada($value)
 * @method static \Illuminate\Database\Query\Builder|\Rezervacija whereMjeraId($value)
 * @method static \Illuminate\Database\Query\Builder|\Rezervacija whereKolicina($value)
 * @method static \Illuminate\Database\Query\Builder|\Rezervacija whereInstruktorId($value)
 * @method static \Illuminate\Database\Query\Builder|\Rezervacija wherePredmetId($value)
 * @method static \Illuminate\Database\Query\Builder|\Rezervacija whereUcionicaId($value)
 * @method static \Illuminate\Database\Query\Builder|\Rezervacija whereNapomena($value)
 * @method static \Illuminate\Database\Query\Builder|\Rezervacija whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Rezervacija whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Rezervacija whereTecaj($value) 
 */

class Rezervacija extends Eloquent {
	const NOT_FOUND_MESSAGE = 'Zadana rezervacija nije pronađena u sustavu.';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'rezervacije';

	protected $guarded = array('id');

	public function instruktor()
	{
		return $this->belongsTo('User','instruktor_id');
	}

	public function ucionica()
	{
		return $this->belongsTo('Ucionica','ucionica_id');
	}

	public function predmet()
	{
		return $this->belongsTo('Predmet','predmet_id');
	}

	public function naplata()
	{
		return $this->hasOne('Naplata');
	}

	public function mjera()
	{
		return $this->belongsTo('Mjera','mjera_id');
	}

	public function kraj_rada()
	{
		$dt = new DateTime($this->pocetak_rada);
		$dt->add(new DateInterval('PT'.$this->mjera->trajanje*$this->kolicina.'M'));
		return $dt->format('Y-m-d H:i:s');
	}

	public function klijenti()
	{
		return $this->belongsToMany('Klijent', 'klijent_rezervacija')
		->withPivot('missed');
	}

    public function scopeNenaplacene($query) {
        return $query->where('tecaj', 0)
            ->where('pocetak_rada', '<', DB::Raw('NOW()'))
            ->has('naplata', '=', 0)
            ->orderBy('pocetak_rada', 'desc');
    }

        /**
     * 
     * @return string
     */
    public function link() {
        $ime = 'Uklonjen predmet';
        if ($this->predmet) {
            $ime = $this->predmet->ime;
        }
        if (Auth::user()->hasPermission(Permission::PERMISSION_FOREIGN_REZERVACIJA_HANDLING)||
                (Auth::user()->hasPermission(Permission::PERMISSION_OWN_REZERVACIJA_HANDLING)
                && $this->instruktor_id == Auth::id())) {
            return link_to_route('Rezervacija.show', $ime, array('id' => $this->id));
        }
        return $ime;
    }

    /**
         * 
         * @param array $input
         * @return string|null
         */
	public function getErrorOrSync($input){
		if(!is_array($input))
			return 'Wrong Input';

		//provjera postojanja potrebnih podataka
			//provjera postojanja podataka za instruktora
				if(!isset($input['instruktor_id']))
					return 'Djelatnik je obvezan.';
				$instruktor_id = $input['instruktor_id'];
			//kraj provjere postojanja podataka za instruktora

			//provjera postojanja podataka za vrijeme pocetka
				if(!isset($input['datum']))
					return 'Datum je obvezan.';
				$datum = $input['datum'];

				if(!isset($input['startHour']))
					return 'Sat početka je obvezan.';
				$startHour = $input['startHour'];

				if(!isset($input['startMinute']))
					return 'Minuta početka je obvezna.';
				$startMinute = $input['startMinute'];
            //kraj provjere postojanja podataka za vrijeme pocetka

            //provjera postojanja podataka za tecaj
                $tecaj = false;
                if(isset($input['tecaj']))
                    $tecaj = $input['tecaj'];

                $tecaj_broj_polaznika = null;
                if ($tecaj) {
                    if(!isset($input['tecaj_broj_polaznika']))
                        return 'Broj polaznika je obvezan za tečaj.';
                    $tecaj_broj_polaznika = $input['tecaj_broj_polaznika'];
                }
            //kraj provjere postojanja podataka za tecaj

			//provjera postojanja podataka za trajanje instrukcija
				if(!isset($input['kolicina']))
					return 'Trajanje instrukcija je obvezno.';
				$kolicina = $input['kolicina'];

				if(!isset($input['mjera_id']))
					return 'Mjera je obvezna.';
				$mjera_id = $input['mjera_id'];
                                
				if(!isset($input['endHour']))
					return 'Sat završetka je obvezan.';
				$endHour = $input['endHour'];

				if(!isset($input['endMinute']))
					return 'Minuta završetka je obvezna.';
				$endMinute = $input['endMinute'];
			//kraj provjere postojanja podataka za trajanje instrukcija

			//provjera postojanja podataka za predmet
				if(!isset($input['predmet_id']))
					return 'Predmeta je obvezan.';
				$predmet_id = $input['predmet_id'];
			//kraj provjere postojanja podataka za predmet

			//provjera postojanja podataka za polaznike
				//izgradnja niza polaznika
				$polaznici = array();
				for($i = 1; isset($input['form-klijenti-item-'.$i.'-broj_mobitela']) ||
					isset($input['form-klijenti-item-'.$i.'-ime']); $i++){

					//trebaju i ime i prezime
					if(!isset($input['form-klijenti-item-'.$i.'-broj_mobitela']) ||
						!isset($input['form-klijenti-item-'.$i.'-ime']))
						return 'Niste unijeli sve potrebne podatke za polaznika u '.$i.'. redu.';

					$ime = $input['form-klijenti-item-'.$i.'-ime'];
					$broj_mobitela = $input['form-klijenti-item-'.$i.'-broj_mobitela'];
					//provjera potpunosti imena i prezimena
					if(empty($ime) || empty($broj_mobitela))
						return 'Niste unijeli sve potrebne podatke za polaznika u '.$i.'. redu.';

					//check if multiple broj_mobetela have the same value
					foreach ($polaznici as $index => $polaznik) {
						if($polaznik->broj_mobitela == $broj_mobitela){
							if($polaznik->ime == $ime)
							    return 'Višestruko ste unijeli istog polaznika ('.$ime.')';
							else return 'Unijeli ste isti broj mobitela za različite polaznike ('.
								$polaznik->ime.' i '.$ime.').';
						}
					}
					$polaznik = new Klijent();
					$polaznik->broj_mobitela = $polaznik->getStorableBrojMobitela($broj_mobitela);
					$polaznik->ime = $ime;
					$polaznici[] = $polaznik;
				}
				//kraj izgradnje niza polaznika
			//kraj provjere postojanja podataka za polaznike

			//provjera postojanja podataka za učionicu
				if(!isset($input['ucionica_id']))
					return 'Učionica je obvezna.';
				$ucionica_id = $input['ucionica_id'];
			//kraj provjere postojanja podataka za učionicu

			//provjer postojanja napomene
				if(isset($input['napomena']))
					$napomena = $input['napomena'];
				else $napomena = '';
			//kraj provjere postojanja napomene
		//kraj provjere postojanja potrebnih podataka

        $broj_polaznika = $tecaj ? $tecaj_broj_polaznika : count($polaznici);
		//provjera dozvoljenih vrijednosti podataka koji se ne odnose na relacije
			//provjera dozvoljene vrijednosti za vrijeme pocetka
				$dto = (new DateTime($datum))->setTime($startHour, $startMinute);
				if($dto < new DateTime() && !(Auth::check() && Auth::user()
                                        ->hasPermission(Permission::PERMISSION_EDIT_STARTED_REZERVACIJA)))
					return 'Zadani početak rada je prošao. Trebate imati posebnu dozvolu za rezerviranje u prošlosti.';
				$pocetak_rada = $dto->format('Y-m-d H:i:s');
			//kraj provjere dozvoljene vrijednosti za vrijeme pocetka

			//provjera dozvoljene vrijednosti za trajanje
				if($kolicina < 1)
					return 'Trajanje instrukcija mora biti barem 1.';
                                $dto2 = (new DateTime($datum))->setTime($endHour, $endMinute);
                                if($dto >= $dto2)
                                    return "Kraj instrukcija mora biti poslje početka.";
				$kraj_rada = $dto2->format('Y-m-d H:i:s');
			//kraj provjere dozvoljene vrijednosti za trajanje

			//provjera broja polaznika
				if($broj_polaznika < 1)
					return 'Potreban je barem 1 polaznik.';
			//kraj provjere broja polaznika
                        
                        //provjera duljine napomene
                            if (strlen($napomena) > 255) {
                                    return 'Napomena treba biti kraća od 255 znakova. Trenutna ima ' . strlen($napomena) . '.';
                            }
                     //kraj porvjere duljine napomene
		//kraj provjere dozvoljenih vrijednosti podataka koji se ne odnose na relacije

		//provjera postojanja referenciranih unosa
			$djelatnik = User::find($instruktor_id);
			if(!$djelatnik)
				return User::NOT_FOUND_MESSAGE;

			$predmet = Predmet::find($predmet_id);
			if(!$predmet)
				return Predmet::NOT_FOUND_MESSAGE;

			$ucionica = Ucionica::find($ucionica_id);
			if(!$ucionica)
				return Ucionica::NOT_FOUND_MESSAGE;

			$mjera = Mjera::find($mjera_id);
			if(!$mjera)
				return Mjera::NOT_FOUND_MESSAGE;

			//provjera usklađenosti polaznika s bazom podataka
				foreach($polaznici as $key => $polaznik){
					$model = Klijent::find($polaznik->broj_mobitela);
					if($model && $model->ime != $polaznik->ime)
						return 'U sustavu već postoji klijent s brojem mobitela '.$model->broj_mobitela.'.';
					if(!$model)
						$polaznik->save();
				}
			//kraj provjere usklađenosti polaznika s bazom podataka
		//kraj provjere postojanja referenciranih unosa

		//ostale provjere relacija
			//provjera završetka instrukcija
                                if($dto2 > $dto->setTime(BaseController::END_HOUR, 0))
                                        return "Kraj radnog vremena je u ".BaseController::END_HOUR." sata.";
                                $dto->setTime($startHour, $startMinute);
                                $dto->add(new DateInterval('PT'.($mjera->trajanje*$kolicina).'M'));
                                if($dto > $dto2)
                                    return "Ne stignete odratiti $kolicina $mjera->znacenje u zadanom vremenu";
			//kraj provjere završetka instrukcija

            //provjera neradnih dana
            if (!Auth::user()->hasPermission(Permission::PERMISSION_IGNORE_NERADNI_DAN)) {
                $neradniDaniNaOdabraniDatum = NeradniDan::datum($datum)->get();
                if (count($neradniDaniNaOdabraniDatum) > 0) {
                    return 'Na odabrani datum se ne radi: ' . $neradniDaniNaOdabraniDatum[0]->naziv;
                }
            }
            //kraj provjere neradnih dana

			//provjera učionice
				//provjera broj učenika
					if($ucionica->max_broj_ucenika < $broj_polaznika)
						return 'Odabrana učionica nema dovoljnu veličinu.';
				//kraj provjere broj učenika

				//provjera zauzetosti učionice
					$preklapanja = Rezervacija::where('ucionica_id', '=', $ucionica->id)
					->where('pocetak_rada', '<', $kraj_rada)
					->where('kraj_rada', '>', $pocetak_rada);
					if($this->id)
					    $preklapanja = $preklapanja->where('rezervacije.id', '!=', $this->id);
					$preklapanja = $preklapanja->count();
					if($preklapanja > 0)
						return 'U zdanome vremenu je odabrana učionica zauzeta.';
				//kraj provjere zauzetosti učionice
			//kraj provjere učionice

			//provjera zauzetosti instruktora
				$preklapanja = Rezervacija::where('instruktor_id', '=', $instruktor_id)
				->where('pocetak_rada', '<', $kraj_rada)
				->where('kraj_rada', '>', $pocetak_rada);
				if($this->id)
				    $preklapanja = $preklapanja->where('rezervacije.id', '!=', $this->id);
				$preklapanja = $preklapanja->count();
				if($preklapanja > 0)
					return 'U zadanome vremenu ste već zauzeti. Provjerite raspored.';
			//kraj provjere zauzetosti instruktora
                        
                        //Provjera dozvole predavanja zadanog predmeta
                                if($djelatnik->predmeti()->where('predmeti.id', '=', $predmet_id)->count() < 1)
					return 'Odabrani instruktor nema dozvolu predavati zadani predmet.';
                        //Kraj provjere dozvole predavanja zadanog predmeta
                        
                        //Provjera dozvole održavanja tečaja
                                if($tecaj && !$djelatnik->hasPermission(Permission::PERMISSION_TECAJ))
					return 'Odabrani instruktor nema dozvolu održavati tečajeve.';
                        //Kraj provjere dozvole održavanja tečaja
		//kraj ostalih provjera relacija

		//sve provjere su uspješno obavljene

		//prodruživanje zadanih vrijednosti
		$this->instruktor_id = $instruktor_id;
		$this->pocetak_rada = $pocetak_rada;
		$this->kolicina = $kolicina;
		$this->mjera_id = $mjera_id;
		$this->kraj_rada = $kraj_rada;
		$this->predmet_id = $predmet_id;
		$this->ucionica_id = $ucionica_id;
        $this->tecaj = $tecaj;
        $this->tecaj_broj_polaznika = $tecaj_broj_polaznika;
		$this->napomena = $napomena;
		//kraj pridruživanja

		//pohrana vrijednosti
		$this->save();

		//sinkronizacija klijenata
		$this->klijenti()->sync(array_map(function($klijent){return $klijent->broj_mobitela;}, $polaznici));

		return null;
	}

}
