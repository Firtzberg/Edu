<?php

class Role extends Eloquent
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['permissions'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = array('name', 'description', 'level');

    /**
     * Users
     *
     * @return object
     */
    public function users()
    {
        return $this->hasMany('User', 'role_id');
    }

    /**
     * Permissions
     *
     * @return object
     */
    public function permissions()
    {
        return $this->belongsToMany('Permission', 'permission_role');
    }

    /**
     * Does the role have a specific permission
     *
     * @param  array|string $perms Single permission or an array of permissions
     *
     * @return boolean
     */
    public function has($perms)
    {
        $perms = !is_array($perms)
            ? array($perms)
            : $perms;

        // Roles permissions list
        $permissions = $this->permissions->lists('name');

        // Check for permission
        foreach ($perms as $perm)
        {
        	if(!in_array($perm, $permissions))
        	    return false;
        }

        return true;
    }
}