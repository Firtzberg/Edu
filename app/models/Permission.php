<?php

/**
 * Permission
 *
 * @property integer $id
 * @property string $ime
 * @property string $opis
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Role[] $roles
 * @method static \Illuminate\Database\Query\Builder|\Permission whereId($value) 
 * @method static \Illuminate\Database\Query\Builder|\Permission whereIme($value) 
 * @method static \Illuminate\Database\Query\Builder|\Permission whereOpis($value) 
 * @method static \Illuminate\Database\Query\Builder|\Permission whereCreatedAt($value) 
 * @method static \Illuminate\Database\Query\Builder|\Permission whereUpdatedAt($value) 
 */

class Permission extends Eloquent
{
    const PERMISSION_VIEW_UCIONICA = 'Gledanje učionica';
    const PERMISSION_VIEW_USER = 'Gledanje djelatnika';
    const PERMISSION_VIEW_PREDMET_KATEGORIJA = 'Gledanje predmetima i kategorija';
    const PERMISSION_VIEW_KLIJENT = 'Gledanje klijenata';
    const PERMISSION_VIEW_ROLE = 'Gledanje uloga';
    const PERMISSION_MANAGE_UCIONICA = 'Upravljanje učionicama';
    const PERMISSION_MANAGE_USER = 'Upravljanje djelatnicima';
    const PERMISSION_MANAGE_PREDMET_KATEGORIJA = 'Upravljanje predmetima i kategorijama';
    const PERMISSION_MANAGE_ROLE = 'Upravljanje ulogama';
    const PERMISSION_MANAGE_KLIJENT = 'Upravljanje klijentima';
    const PERMISSION_REMOVE_UCIONICA = 'Uklonjanje učionica';
    const PERMISSION_REMOVE_USER = 'Uklonjanje djelatnika';
    const PERMISSION_REMOVE_PREDMET_KATEGORIJA = 'Uklonjanje predmeta i kategorija';
    const PERMISSION_REMOVE_ROLE = 'Uklonjanje uloga';
    const PERMISSION_PASSWORD_RESET = 'Promjena zaporke drugom djelatniku';
    const PERMISSION_DOWNLOAD_DATA = 'Preuzimanje podataka(Excell)';
    const PERMISSION_OWN_REZERVACIJA_HANDLING = 'Rezerviranje i naplaćivanje';
    const PERMISSION_FOREIGN_REZERVACIJA_HANDLING = 'Rezerviranje i naplaćivanjeza u ime drugog djelatnika';
    const PERMISSION_REMOVE_STARTED_REZERVACIJA = 'Uklonjanje započete rezervacije';
    const PERMISSION_EDIT_STARTED_REZERVACIJA = 'Uređivanje započete rezervacije';
    const PERMISSION_REMOVE_NALATA = 'Uklonjanje naplate';
    const PERMISSION_SEE_GLOBAL_IZVJESTAJ = 'Uvid u ukupne izvještaje';
    const PERMISSION_SEE_FOREIGN_IZVJESTAJ = 'Uvid u tuđe izvještaje';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = array('name', 'description');

    /**
     * Roles
     *
     * @return object
     */
    public function roles()
    {
        return $this->belongsToMany('Role', 'permission_role');
    }
}