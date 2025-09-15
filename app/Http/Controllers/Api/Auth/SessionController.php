<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SessionController extends Controller
{
      public function signin(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid body',
                'errors' => $validate->errors(),
            ], 400);
        }

        $user = User::firstWhere('email', $request->email);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email not found',
            ], 404);
        }

        if (Auth::attempt($request->only(['email', 'password']))) {
            $token = $user->createToken(env("SECRET_TOKEN"));
            $user['token'] = $token->plainTextToken;
            return response()->json([
                'status' => 'success',
                'message' => 'Signin success',
                'data' => $user
            ], 200);
        }


        return response()->json([
            'status' => 'error',
            'message' => 'Email or password wrong',
        ], 401);
    }

    public function signout(Request $request)
    {
        $user = $request->user();

        $user->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout success',
        ], 200);
    }
}
