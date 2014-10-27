<?php

class Role extends Eloquent
{

    const NOT_FOUND_MESSAGE = 'Zadana uloga nije pronađena u sustavu.';
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
    //protected $with = ['permissions'];

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

    public function link(){
        return link_to_route('Role.show', $this->ime, array('id' => $this->id));
    }

    public function getErrorOrSync($input){
        if(!is_array($input))
            return "Wrong input";

        //provjera postojanja nužnih podataka
        $ime = $this->ime;
        if(!$ime && !isset($input['ime']))
            return 'Ime je obvezno';
        if(isset($input['ime']))
            $ime = $input['ime'];

        if(isset($input['allowed']))
            $allowed = $input['allowed'];
        else $allowed = array();
        if(!$allowed || !is_array($allowed))
            $allowed = array();
        //kraj provjere nužnih podataka

        //provjera zauzetosti imena
        $query = Role::where('ime', '=', $ime);
        if($this->id > 0)
            $query = $query->where('id', '!=', $this->id);
        if($query->count() > 0)
            return 'Već postoji uloga s imenom '.$ime.'.';
        //kraj provjere zauzetosti imena

        //odabir postojećih dozvola
        if(count($allowed) > 0)
            $allowed = Permission::select('id')
            ->whereIn('id', $allowed)
            ->get()
            ->lists('id');

        //pohrana podataka
        $this->ime = $ime;
        if(isset($input['opis']))
            $this->opis = $input['opis'];
        $this->save();

        //pohrana dozvola
        if(count($allowed) > 0)
            $this->permissions()->sync($allowed);
        else $this->permissions()->detach();
    }
}