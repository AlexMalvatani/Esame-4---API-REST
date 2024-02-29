<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comuni;

class ComuniController extends Controller
{
    public function showComuniNames()
    {
        $comuniNames = Comuni::pluck('name');

        return response()->json(['comuni_list' => $comuniNames]);
    }

    public function showRegioniNames()
    {
        $regionsNames = Comuni::pluck('region');

        return response()->json(['regions_list' => $regionsNames]);
    }

    public function showCities()
    {
        $citiesNames = Comuni::pluck('city');

        if ($citiesNames) {
            return response()->json(['cities_list' => $citiesNames]);
        } else {
            return response()->json(['cities_list' => 'No Cities found!'], 404);
        }
    }

    public function showProvinces()
    {
        $provincesNames = Comuni::pluck('province');

        if ($provincesNames) {
            return response()->json(['provinces_list' => $provincesNames]);
        } else {
            return response()->json(['provinces_list' => 'No provinces found!'], 404);
        }
    }

    public function showPostal()
    {
        $postals = Comuni::pluck('postal_code');

        if ($postals) {
            return response()->json(['provinces_list' => $postals]);
        } else {
            return response()->json(['postal_codes_list' => 'No postal codes found!'], 404);
        }
    }
}
