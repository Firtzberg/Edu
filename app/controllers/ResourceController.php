<?php

namespace App\Controller;

use Auth;
use Redirect;

class ResourceController extends App\Controller\BaseController {

    public function __construct() {
        $this->beforeFilter(function() {
            if (!(Auth::check() && Auth::user()->hasPermission($this->deletePermissions))) {
                return Redirect::to('login');
            }
        }, array('only' => array('destroy')));

        $this->beforeFilter(function() {
            if (!(Auth::check() && Auth::user()->hasPermission($this->watchPermissions))) {
                return Redirect::to('login');
            }
        }, array('only' => array('index', '_list', 'show')));

        $this->beforeFilter(function() {
            if (!(Auth::check() && Auth::user()->hasPermission($this->managePermissions))) {
                return Redirect::to('login');
            }
        }, array('only' => array('create', 'store', 'edit', 'update', 'destroy')));
    }

    /**
     *
     * @var array Permissions a user has to have when accessing the destroy method
     */
    protected $deletePermissions = array();

    /**
     * 
     * @param string|array $permission Permission(s) to add to the deletePermissions
     */
    protected function requireDeletePermission($permission) {
        if ($this->deletePermissions == null) {
            $this->deletePermissions = array();
        }
        if (is_array($permission)) {
            $this->deletePermissions = array_merge($this->deletePermissions, $permission);
        } else {
            $this->deletePermissions[] = $permission;
        }
    }

    /**
     *
     * @var array Permissions a user has to have to list and show the resource.
     */
    protected $watchPermissions = array();

    /**
     * 
     * @param string|array $permission Permission(s) to add to the watchPermissions
     */
    protected function requireWatchPermission($permission) {
        if ($this->watchPermissions == null) {
            $this->deletePermissions = array();
        }
        if (is_array($permission)) {
            $this->watchPermissions = array_merge($this->watchPermissions, $permission);
        } else {
            $this->watchPermissions[] = $permission;
        }
    }

    /**
     *
     * @var array Permissions a user has to have to create and edit resources.
     */
    protected $managePermissions = array();

    /**
     * 
     * @param string|array $permission Permission(s) to add to the managePermissions
     */
    protected function requireManagePermission($permission) {
        if ($this->managePermissions == null) {
            $this->managePermissions = array();
        }
        if (is_array($permission)) {
            $this->managePermissions = array_merge($this->managePermissions, $permission);
        } else {
            $this->managePermissions[] = $permission;
        }
    }

}
