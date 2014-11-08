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
	const PERMISSION_ADD_UCIONICA = 'Dodavanje učionice';
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