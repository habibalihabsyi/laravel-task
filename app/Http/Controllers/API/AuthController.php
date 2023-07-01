<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::where('email',$request->email)->first();

        if (empty($user)) {
            return response()->json(['error' => 'Email not registered'], 401);
        }
        try {
            $request->authenticate();
            $token = $request->user()->createToken('authtoken');

            $user = User::find(auth()->id());

            return response()->json(
                [
                    'message' => 'Login Success',

                    'data' => [
                        'token' => $token->plainTextToken,
                        'user' => $user,
                    ],
                ]
            );
        } catch (\Exception $e) {
            // return response()->json(['error' => 'Credentials not valid'], 401);
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
}
