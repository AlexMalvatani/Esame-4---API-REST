<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nation;

class NationsController extends Controller
{
    public function show()
    {
        $nationsList = Nation::pluck('name');

        return response()->json(['nations_list' => $nationsList]);
    }

    public function showIsoCodes()
    {
        $nationsIsoCodes = Nation::pluck('iso_code');

        return response()->json(['nations_list' => $nationsIsoCodes]);
    }

    public function showIsoCode($nation)
    {
        $nationFind = Nation::where('name', $nation)->first();

        if ($nationFind) {
            return response()->json(['nations_iso_code' => $nationFind->iso_code]);
        } else {
            return response()->json(['msg' => 'Nation not found!'], 404);
        }
    }

    public function showPhoneCodes()
    {
        $nationsPhoneCodes = Nation::pluck('phone_code');

        return response()->json(['nations_list' => $nationsPhoneCodes]);
    }

    public function showPhoneCode($nation)
    {
        $nationFind = Nation::where('name', $nation)->first();

        if ($nationFind) {
            return response()->json(['nations_iso_code' => $nationFind->phone_code]);
        } else {
            return response()->json(['msg' => 'Nation not found!'], 404);
        }
    }
}
