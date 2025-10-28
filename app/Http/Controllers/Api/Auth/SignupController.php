<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SignupController  extends Controller
{
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|max:20',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid body',
                'errors' => $validator->errors(),
            ], 400);
        }

        User::create($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Signup success',
        ], 201);
    }
}
