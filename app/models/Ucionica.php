<?php

/**
 * Ucionica
 *
 * @property integer $id
 * @property string $naziv
 * @property integer $max_broj_ucenika
 * @property string $adresa
 * @property integer $kat
 * @property string $opis
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rezervacija[] $rezervacije
 * @method static \Illuminate\Database\Query\Builder|\Ucionica whereId($value) 
 * @method static \Illuminate\Database\Query\Builder|\Ucionica whereNaziv($value) 
 * @method static \Illuminate\Database\Query\Builder|\Ucionica whereMaxBrojUcenika($value) 
 * @method static \Illuminate\Database\Query\Builder|\Ucionica whereAdresa($value) 
 * @method static \Illuminate\Database\Query\Builder|\Ucionica whereKat($value) 
 * @method static \Illuminate\Database\Query\Builder|\Ucionica whereOpis($value) 
 * @method static \Illuminate\Database\Query\Builder|\Ucionica whereCreatedAt($value) 
 * @method static \Illuminate\Database\Query\Builder|\Ucionica whereUpdatedAt($value) 
 */

class Ucionica extends Eloquent {
	const NOT_FOUND_MESSAGE = 'Zadana učionica nije pronađena u sustavu.';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'ucionice';

	protected $guarded = array('id');

	public function rezervacije()
	{
		return $this->hasMany('Rezervacija');
	}

    /**
     * 
     * @return string
     */
    public function link() {
        if (Auth::user()->hasPermission(Permission::PERMISSION_VIEW_UCIONICA)) {
            return link_to_route('Ucionica.show', $this->naziv, array('id' => $this->id));
        }
        return $this->naziv;
    }

    public function getErrorOrSync($input){
        if(!is_array($input))
            return "Wrong input";

        //provjera postojanja nužnih podataka
        //privjera naziva
        $naziv = $this->naziv;
        if(!$naziv && !isset($input['naziv']))
            return 'Naziv je obvezan';
        if(isset($input['naziv']))
            $naziv = $input['naziv'];
        //provjera adrese
        $adresa = $this->adresa;
        if(!$adresa && !isset($input['adresa']))
            return 'Adresa je obvezna';
        if(isset($input['adresa']))
            $adresa = $input['adresa'];
        //provjera kata
        $kat = $this->kat;
        if(!$kat && !isset($input['kat']))
            return 'Kat je obvezan';
        if(isset($input['kat']))
            $kat = $input['kat'];
        //provjera broja učenika
        $max_broj_ucenika = $this->max_broj_ucenika;
        if(!$max_broj_ucenika && !isset($input['max_broj_ucenika']))
            return 'Broj učenika je obvezan';
        if(isset($input['max_broj_ucenika']))
            $max_broj_ucenika = $input['max_broj_ucenika'];
        //kraj provjere nužnih podataka
        
        //provjera vrijednosti podataka
        if($max_broj_ucenika < 1)
            return 'Kapacitet učionice ne može biti manji od 1.';
        //kraj provjere vrijednosti podataka

        //provjera zauzetosti naziva
        $query = Ucionica::where('naziv', '=', $naziv);
        if($this->id > 0)
            $query = $query->where('id', '!=', $this->id);
        if($query->count() > 0)
            return 'Već postoji učionica s nazivom '.$ime.'.';
        //kraj provjere zauzetosti imena

        //pohrana podataka
        $this->naziv = $naziv;
        $this->adresa = $adresa;
        $this->kat = $kat;
        $this->max_broj_ucenika = $max_broj_ucenika;
        
        if(isset($input['opis']))
            $this->opis = $input['opis'];
        $this->save();
    }

}
