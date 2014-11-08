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

	public function link(){
		return link_to_route('Ucionica.show', $this->naziv, array('id' => $this->id));
	}

}
