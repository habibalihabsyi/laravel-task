<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function store(Request $request){
        // return $request->all();
        try {
            $validated = $this->validate($request, [
                'name' => 'required',
                'email' => 'required',
                'password' => 'required',
            ]);
            $user = User::create($validated);
            $user->assignRole('Member');
            return $this->successResponse($user, 'Successfully add user', Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            return $this->unknownResponse($th);
        }
    }
}
