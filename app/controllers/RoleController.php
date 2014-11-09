<?php

namespace App\Controller;

use App\Model\Permission;
use App\Model\Role;
use Redirect;
use Session;

class RoleController extends App\Controller\ResourceController {
    
    public function __construct() {
        parent::__construct();
        
        $this->requireManagePermission(Permission::PERMISSION_MANAGE_ROLE);
        $this->requireWatchPermission(Permission::PERMISSION_MANAGE_ROLE);
        $this->requireDeletePermission(Permission::PERMISSION_REMOVE_ROLE);
    }
    
    private function itemNotFound(){
		Session::flash(self::DANGER_MESSAGE_KEY, Role::NOT_FOUND_MESSAGE);
  		return Redirect::route('Role.index');
    }
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return View::make('Role.index')
		->with('list', $this->_list());
	}

	public function _list($page = 1, $searchString = null){
		if(!empty($searchString)){
			$roles = Role::where('ime', 'like', '%'.$searchString.'%')
			->orderBy('ime');
		}
		else
			$roles = Role::orderBy('ime');
		if($page != 1)
			Paginator::setCurrentPage($page);
		$roles = $roles->paginate(10);
		$v = View::make('Role.list')
		->with('roles', $roles);
		if(Request::ajax())
			return $v->renderSections()['list'];
		return $v;
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('Role.create');
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$role = new Role();
		$error =  $role->getErrorOrSync(Input::all());
		if($error != null)
			return Redirect::route('Role.create')
			->withInput()
			->with(self::DANGER_MESSAGE_KEY, $error);
		return Redirect::route('Role.show', array('id' => $role->id));
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$role = Role::find($id);
		if(!$role)
			return $this->itemNotFound();
		return View::make('Role.show')
		->with('role', $role);
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$role = Role::find($id);
		if(!$role)
			return $this->itemNotFound();
		return View::make('Role.create')
		->with('role', $role);
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$role = Role::find($id);
		if(!$role)
			return $this->itemNotFound();

		$error =  $role->getErrorOrSync(Input::all());
		if($error != null)
			return Redirect::route('Role.edit', array('id' => $id))
			->withInput()
			->with(self::DANGER_MESSAGE_KEY, $error);

		return Redirect::route('Role.show', array('id' => $role->id));
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$role = Role::find($id);
		if(!$role)
			return $this->itemNotFound();
		$role->delete();
		Session::flash(self::SUCCESS_MESSAGE_KEY, 'Uloga je uspješno uklonjena!');
		return Redirect::route('Role.index');
	}


}
