<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function index()
    {
        $sampleResponse = [
            "service_name" => "PHP Service APP",
            "status" => "Running"
        ];
        return $sampleResponse;
    }

    public function users($id = null)
    {
        $users = collect(
        [
            [
                "id"=> 1,
                "name"=> "Sumatrana",
                "email"=> "sumatrana@gmail.com",
                "address"=> "Padang",
                "gender"=> "Laki-laki",
            ],
            [
                "id"=> 2,
                "name"=> "Jawarianto",
                "email"=> "jawarianto@gmail.com",
                "address"=> "Cimahi",
                "gender"=> "Laki-laki"
            ],
            [
                "id"=> 3,
                "name"=> "Kalimantanio",
                "email"=> "kalimantanio@gmail.com",
                "address"=> "Samarinda",
                "gender"=> "Laki-laki"
            ],
            [
                "id"=> 4,
                "name"=> "Sulawesiani",
                "email"=> "sulawesiani@gmail.com",
                "address"=> "Makasar",
                "gender"=> "Perempuan"
            ],
            [
                "id"=> 5,
                "name"=> "Papuani",
                "email"=> "papuani@gmail.com",
                "address"=> "Jayapura",
                "gender"=> "Perempuan"
            ]
        ]);

        if ($id === null) {
            return $users;
        }

        $user = $users->firstWhere('id', $id);
        if (!$user) {
            abort(404);
        }

        return $user;
    }
}
