<?php

class Mjera extends Eloquent{
	const NOT_FOUND_MESSAGE = 'Zadana mjera nije pronađena u sustavu.';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'mjere';

	//Disables timestamps
	public $timestamps = false;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $guarded = array('id');

}