<?php

namespace App\Requests\Praticien;


use App\Requests\Request;

class UpdatePraticienRequest extends Request
{
    public function rules(): array
    {
        return [
            'nom' => 'required',
            'prenom' => 'required',
            'email' => 'required',
            'phone' => 'nullable',
            'speciality' => 'required',
            'adelie' => 'required',
            'numero' => 'required',
            'rue' => 'required',
            'ville' => 'required',
            'codePostal' => 'required',
        ];
    }

    public function messages(): array
    {
        return [];
    }
}
