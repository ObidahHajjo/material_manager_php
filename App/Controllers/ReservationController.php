<?php

namespace App\Controllers;


class ReservationController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function create()
    {
        $this->view('App/CreateReservationView', [
            'title' => 'Create Reservation',
            'active' => 'newReservation'
        ]);
    }
}
