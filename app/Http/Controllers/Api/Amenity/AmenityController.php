<?php

namespace App\Http\Controllers\Api\Amenity;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use Illuminate\Http\Request;

class AmenityController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Get all amenity success',
            'data' => Amenity::all(),
        ], 200);
    }
}
