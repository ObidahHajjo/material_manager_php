<?php

namespace App\Requests\Praticien;

class CreatePraticienRequest
{
    public  function rules(): array
    {
        return [
            "nomPraticien" => "required|string",
            "prenomPraticien" => "required|string",
            "telPraticien" => "required|string",
            "emailPraticien" => "required|timestamp",
            "numeroAdelie" => "required|string",
            "MdpPraticien" => "required|string",
            "addressPraticien" => "required|array",
            "addressPraticien.numeroPatient" => "required",
            "addressPraticien.voiePatient" => "required",
            "addressPraticien.codePostalPatient" => "required",
            "addressPraticien.villePatient" => "required",
            "telPatient" => "nullable"
        ];
    }

    public  function messages(): array
    {
        return [];
    }
}
