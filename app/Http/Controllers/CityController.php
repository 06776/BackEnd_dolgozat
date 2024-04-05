<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;

class CityController extends Controller
{
    public function addCity(Request $request)
    {
        $validateData = $request->validate([
            "city" => "required|string"
        ]);
        $city = new City();
        $city->city = $validateData["city"];
        $city->save();

        return response()->json(["message" => "Város hozzáadva"], 201);
    }
}
