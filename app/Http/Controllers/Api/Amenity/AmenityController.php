<?php

namespace App\Http\Controllers\Api\Amenity;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use Illuminate\Http\Request;

class AmenityController extends Controller
{
    public function index(Request $request)
    {
        $amenities = Amenity::query()
            ->when($request->has('type'), function ($query) use ($request) {
                $query->where('type', $request->get('type'));
            })
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Get amenities success',
            'data' => $amenities,
        ], 200);
    }
}
