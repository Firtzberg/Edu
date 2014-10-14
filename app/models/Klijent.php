<?php

class Klijent extends Eloquent{
	const NOT_FOUND_MESSAGE = 'Zadani klijent nije pronađen u sustavu.';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'klijenti';

	protected $fillable = array('broj_mobitela', 'ime', 'facebook', 'email');
	protected $guarded = array();

	//Enables primary key not to be int
    public $incrementing = false;

    protected $primaryKey = 'broj_mobitela';

    public function rezervacije()
    {
    	return $this->belongsToMany('Rezervacija','klijent_rezervacija')
    	->withPivot('missed');
    }
}