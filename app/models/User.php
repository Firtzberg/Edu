<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

/**
 * User
 *
 * @property-read \Role $role
 * @property-read \Illuminate\Database\Eloquent\Collection|\Predmet[] $predmeti
 * @property integer $id
 * @property string $name
 * @property string $remember_token
 * @property boolean $is_admin
 * @property string $broj_mobitela
 * @property string $email
 * @property string $lozinka
 * @property string $boja
 * @property integer $role_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereIsAdmin($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereBrojMobitela($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereLozinka($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereBoja($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereRoleId($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\User whereUpdatedAt($value)
 */
class User extends Eloquent implements UserInterface, RemindableInterface {
	const NOT_FOUND_MESSAGE = 'Zadani instruktor nije pronađen u sustavu.';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('lozinka');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->lozinka;
	}

	/**
	 * Get the token value for the "remember me" session.
	 *
	 * @return string
	 */
	public function getRememberToken()
	{
		return $this->remember_token;
	}

	/**
	 * Set the token value for the "remember me" session.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function setRememberToken($value)
	{
		$this->remember_token = $value;
	}

	/**
	 * Get the column name for the "remember me" token.
	 *
	 * @return string
	 */
	public function getRememberTokenName()
	{
		return 'remember_token';
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

	public function klijenti(){
		return Klijent::select('klijenti.broj_mobitela', 'klijenti.ime')
		->whereExists(function($query){
			$query->from('klijent_rezervacija')
			->join('rezervacije', 'rezervacije.id', '=', 'klijent_rezervacija.rezervacija_id')
			->join('users', 'users.id', '=', 'rezervacije.instruktor_id')
			->where('users.id', '=', Auth::id())
			->whereRaw('klijent_rezervacija.klijent_id=klijenti.broj_mobitela');
		});
	}

	public function role(){
		return $this->belongsTo('Role', 'role_id');
	}

	public function predmeti(){
		return $this->belongsToMany('Predmet', 'predmet_user');
	}

	public function link(){
		return link_to_route('Instruktor.show', $this->name, array('id' => $this->id));
	}

	public function getErrorOrSync($input){
		if(!is_array($input))
			return "Wrong input";

		//provjera prisutnosti potrebnih podataka
		$name = $this->name;
		if(!$name && !isset($input['name']))
			return "Ime je obvezno.";
		if(isset($input['name']))
			$name = $input['name'];

		if(!isset($input['role_id']))
			return "Odaberite ulogu.";
		$role_id = $input['role_id'];
		//kraj provjere prisutnosti potrebnih podataka

		//provjera jedinstvenosti imena
		$query = User::where('name', '=', $name);
		if($this->id > 0)
			$query = $query->where('id', '!=', $this->id);
		if($query->count() > 0)
			return 'Već postoji osoba s odabranim imenom.';
		//kraj provjere jedinstvenosti imena

		//provjera postojanja uloge
		if(Role::where('id', '=', $role_id)->count() < 1)
			return 'Odabrana uloga nije pronađena u sustavu.';
		//kraj provjere postojanja uloge

		//pridruživanje vrijednosti
		$this->name = $name;
		$this->role_id = $role_id;
		if(isset($input['boja']))
			$this->boja = substr($input['boja'], 1);
		if(isset($input['broj_mobitela']))
			$this->broj_mobitela = $input['broj_mobitela'];
		if(isset($input['email']))
			$this->email = $input['email'];
		if(isset($input['facebook']))
			$this->facebook = $input['facebook'];
		//kraj pridruživanja vrijednosti

		//odabir predmeta
        if(isset($input['allowed']))
            $predmet_ids = $input['allowed'];
        else $predmet_ids = array();
        if(!$predmet_ids || !is_array($predmet_ids))
            $predmet_ids = array();

        if(count($predmet_ids) > 0)
            $predmet_ids = Predmet::select('id')
            ->whereIn('id', $predmet_ids)
            ->get()
            ->lists('id');
        //kraj odabira predmeta

		$this->save();
            
        if(count($predmet_ids) > 0)
            $this->predmeti()->sync($predmet_ids);
        else $this->predmeti()->detach();
	}

}
