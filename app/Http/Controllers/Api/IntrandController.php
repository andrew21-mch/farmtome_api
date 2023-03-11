<?php

namespace App\Http\Controllers;

use App\Models\Intrand;
use Illuminate\Http\Request;

class IntrandController extends Controller
{
    public function index()
    {
        $intrands = Intrand::all();

        return response()->json([
            'success' => true,
            'message' => 'List Data Intrand',
            'data' => $intrands
        ], 200);
    }
}
