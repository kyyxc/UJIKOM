<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Hotel;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Return a list of countries with metadata and hotel counts.
     */
    public function index(Request $request)
    {
        // Fetch countries from database
        $query = Country::query();

        // Only show active countries by default
        if (!$request->has('show_inactive')) {
            $query->where('is_active', true);
        }

        // Paginate with 12 items per page
        $countries = $query->orderBy('name', 'asc')->paginate(12);

        // Fetch hotel counts grouped by country in one query
        $counts = Hotel::selectRaw('country, COUNT(*) as hotel_count')
            ->where('is_active', true)
            ->groupBy('country')
            ->pluck('hotel_count', 'country')
            ->toArray();

        // Merge counts into countries
        $result = $countries->map(function ($country) use ($counts) {
            return [
                'id' => $country->id,
                'name' => $country->name,
                'code' => $country->code,
                'description' => $country->description,
                'image' => $country->image,
                'is_active' => $country->is_active,
                'hotelCount' => $counts[$country->name] ?? 0,
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Get countries',
            'data' => $result,
            'pagination' => [
                'current_page' => $countries->currentPage(),
                'per_page' => $countries->perPage(),
                'total' => $countries->total(),
                'last_page' => $countries->lastPage(),
                'from' => $countries->firstItem(),
                'to' => $countries->lastItem(),
            ],
        ], 200);
    }
}
