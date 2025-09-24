<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'hotel_id' => 'required|exists:hotels,id',
            'room_number' => 'required|string|max:50',
            'room_type' => 'required|in:single,double,deluxe,suite',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'price_per_night' => 'required|numeric|min:0',
            'status' => 'required|in:available,occupied,maintenance',

            'amenities' => 'array',
            'amenities.*' => 'integer|exists:amenities,id',

            'images' => 'array',
            'images.*' => 'required|file|mimes:png,jpg,jpeg,webp|max:2048',
        ];
    }
}
