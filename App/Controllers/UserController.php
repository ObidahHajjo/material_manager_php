<?php

namespace App\Controllers;

use App\Eloquents\UserEloquent;

class UserController extends Controller
{
    private UserEloquent $userEloquent;

    public function __construct()
    {
        parent::__construct();
        $this->userEloquent = new UserEloquent();
    }

    public function show()
    {
        $users = $this->userEloquent->all();
        $this->view('App/userManagmentView', [
            'title' => 'User Manager',
            'users' => $users,
            'active' => 'users'
        ]);
    }
}
