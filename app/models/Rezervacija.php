<?php

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

	public function getErrorOrSync($input){
		if(!is_array($input))
			return 'Wrong Input';

		//provjera postojanja potrebnih podataka
			//provjera postojanja podataka za instruktora
				if(!isset($input['instruktor_id']))
					return 'Instruktor je obvezan.';
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

			//provjera postojanja podataka za trajanje instrukcija
				if(!isset($input['kolicina']))
					return 'Trajanje instrukcija je obvezno.';
				$kolicina = $input['kolicina'];

				if(!isset($input['mjera_id']))
					return 'Mjera je obvezna.';
				$mjera_id = $input['mjera_id'];
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
		//kraj provjere postojanja potrebnih podataka

		//provjera dozvoljenih vrijednosti podataka koji se ne odnose na relacije
			//provjera dozvoljene vrijednosti za vrijeme pocetka
				$dto = new DateTime($datum);
				$dto = $dto->setTime($startHour, $startMinute);
				if($dto < new DateTime() && !(Auth::check() && Auth::user()->is_admin))
					return 'Zadani početak rada je prošao. Nije moguće napraviti rezervaciju u prošlosti.';
				$pocetak_rada = $dto->format('Y-m-d H:i:s');
			//kraj provjere dozvoljene vrijednosti za vrijeme pocetka

			//provjera dozvoljene vrijednosti za trajanje
				if($kolicina < 1)
					return 'Trajanje instrukcija mora biti barem 1.';
			//kraj provjere dozvoljene vrijednosti za trajanje

			//provjera broja polaznika
				if(count($polaznici) < 1)
					return 'Potreban je barem 1 polaznik.';
			//kraj provjere broja polaznika
		//kraj provjere dozvoljenih vrijednosti podataka koji se ne odnose na relacije

		//provjera postojanja referenciranih unosa
			$instruktor = User::find($instruktor_id);
			if(!$instruktor)
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
				$dto->add(new DateInterval('PT'.($mjera->trajanje*$kolicina).'M'));
				$kraj_rada = $dto->format('Y-m-d H:i:s');
			//kraj provjere završetka instrukcija

			//provjera učionice
				//provjera broj učenika
					if($ucionica->max_broj_ucenika < count($polaznici))
						return 'Odabrana učionica nema dovoljnu veličinu.';
				//kraj provjere broj učenika

				//provjera zauzetosti učionice
					$preklapanja = Rezervacija::where('ucionica_id', '=', $ucionica->id)
					->join('mjere', 'mjere.id', '=', 'rezervacije.mjera_id')
					->where('pocetak_rada', '<', $kraj_rada)
					->whereRaw("timestampadd(MINUTE, kolicina*trajanje, pocetak_rada) > '".$pocetak_rada."'");
					if($this->id)
					    $preklapanja = $preklapanja->where('rezervacije.id', '!=', $this->id);
					$preklapanja = $preklapanja->count();
					if($preklapanja > 0)
						return 'U zdanome vremenu je odabrana učionica zauzeta.';
				//kraj provjere zauzetosti učionice
			//kraj provjere učionice

			//provjera zauzetosti instruktora
				$preklapanja = Rezervacija::where('instruktor_id', '=', $instruktor_id)
				->join('mjere', 'mjere.id', '=', 'rezervacije.mjera_id')
				->where('pocetak_rada', '<', $kraj_rada)
				->whereRaw("timestampadd(MINUTE, kolicina*trajanje, pocetak_rada) > '".$pocetak_rada."'");
				if($this->id)
				    $preklapanja = $preklapanja->where('rezervacije.id', '!=', $this->id);
				$preklapanja = $preklapanja->count();
				if($preklapanja > 0)
					return 'U zadanome vremenu ste već zauzeti. Provjerite raspored.';
			//kraj provjere zauzetosti instruktora
		//kraj ostalih provjera relacija

		//sve provjere su uspješno obavljene

		//prodruživanje zadanih vrijednosti
		$this->instruktor_id = $instruktor_id;
		$this->pocetak_rada = $pocetak_rada;
		$this->kolicina = $kolicina;
		$this->mjera_id = $mjera_id;
		$this->predmet_id = $predmet_id;
		$this->ucionica_id = $ucionica_id;
		//kraj pridruživanja

		//pohrana vrijednosti
		$this->save();

		//sinkronizacija klijenata
		$this->klijenti()->sync(array_map(function($klijent){return $klijent->broj_mobitela;}, $polaznici));

		return null;
	}

}
