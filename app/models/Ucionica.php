<?php

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
