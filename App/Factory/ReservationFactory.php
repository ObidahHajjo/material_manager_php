<?php

namespace App\Factory;

use App\Models\Reservation;

class ReservationFactory
{
    public static function create(array $data): Reservation
    {
        return new Reservation($data);
    }
}
