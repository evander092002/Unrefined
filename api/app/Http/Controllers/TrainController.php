<?php

namespace App\Http\Controllers;

use App\Models\Train;
use Illuminate\Http\Request;

class TrainController extends Controller
{
    public function index() 
    {
        $trains = Train::all();

        if ($trains->count() > 0) {
            return response()->json(['trains' => $trains]);
        } else {
            return response()->json([
                'trains' => 'No trains Found'
            ]);
        }
    }
}
