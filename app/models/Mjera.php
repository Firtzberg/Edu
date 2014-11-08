<?php

/**
 * Mjera
 *
 * @property integer $id
 * @property string $simbol
 * @property string $znacenje
 * @property integer $trajanje
 * @method static \Illuminate\Database\Query\Builder|\Mjera whereId($value) 
 * @method static \Illuminate\Database\Query\Builder|\Mjera whereSimbol($value) 
 * @method static \Illuminate\Database\Query\Builder|\Mjera whereZnacenje($value) 
 * @method static \Illuminate\Database\Query\Builder|\Mjera whereTrajanje($value) 
 */
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