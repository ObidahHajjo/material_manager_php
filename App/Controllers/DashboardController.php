<?php

namespace App\Controllers;


class DashboardController extends Controller
{

    public function show()
    {
        $this->view('App/DashboardView', [
            'title' => "Dashboard",
            'active' => 'dashboard'
        ]);
    }
}
