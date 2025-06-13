<?php

namespace App\Requests\Reservations;

use App\Requests\Request;

class CreateReservationRequest extends Request
{
    public function rules(): array
    {
        return [
            "start_date" => "required|datetime",
            "end_date" => "required|datetime",
            "materials" => "required|array",
            "materials.*" => "required|int"
        ];
    }
}
