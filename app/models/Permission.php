<?php

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