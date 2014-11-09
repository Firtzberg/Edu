<?php

/**
 * Klijent
 *
 * @property string $broj_mobitela
 * @property string $ime
 * @property string $email
 * @property string $facebook
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Rezervacija[] $rezervacije
 * @method static \Illuminate\Database\Query\Builder|\Klijent whereBrojMobitela($value) 
 * @method static \Illuminate\Database\Query\Builder|\Klijent whereIme($value) 
 * @method static \Illuminate\Database\Query\Builder|\Klijent whereEmail($value) 
 * @method static \Illuminate\Database\Query\Builder|\Klijent whereFacebook($value) 
 * @method static \Illuminate\Database\Query\Builder|\Klijent whereCreatedAt($value) 
 * @method static \Illuminate\Database\Query\Builder|\Klijent whereUpdatedAt($value) 
 */
namespace App\Model;
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

    /**
     * 
     * @return string
     */
    public function getReadableBrojMobitela(){
    	$broj_mobitela = $this->broj_mobitela;
		if(substr($broj_mobitela, 0, 5) == '00385')
			$broj_mobitela = '0'.substr($broj_mobitela, 5);
		if(strlen($broj_mobitela) > 3 && $broj_mobitela[0] == '0'){
			$broj_mobitela = substr_replace($broj_mobitela, ' ', 3, 0);
			if(strlen($broj_mobitela) > 7)
				$broj_mobitela = substr_replace($broj_mobitela, ' ', 7, 0);
		}
		return $broj_mobitela;
    }

    /**
     * 
     * @param string $broj_mobitela
     * @return stirng
     */
    public function getStorableBrojMobitela($broj_mobitela){
    	if(strlen($broj_mobitela) > 0 && $broj_mobitela[0] == '+')
    		$broj_mobitela = '00'.substr($broj_mobitela, 1);
		if(strlen($broj_mobitela) > 1)
			if($broj_mobitela[0] == '0' && $broj_mobitela[1] != '0')
				$broj_mobitela = '00385'.substr($broj_mobitela, 1);
		$broj_mobitela = str_replace('(0)', '', $broj_mobitela);
		$chars = str_split($broj_mobitela);
		$chars = array_filter($chars, function($char){return ($char >='0' && $char <= '9');});
		$broj_mobitela = implode($chars);
		return $broj_mobitela;
    }

    /**
     * 
     * @return string
     */
	public function link(){
            if (Auth::user()->hasPermission(Permission::PERMISSION_MANAGE_KLIJENT)) {
            return link_to_route('Klijent.show', $this->ime, array('id' => $this->broj_mobitela));
        }
        return $this->ime;
	}
}