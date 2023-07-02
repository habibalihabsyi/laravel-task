<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:Super Admin|Admin']);
    }
    public function index(){
        try {
            $users = User::whereDoesntHave('roles', fn($q) => $q->where('name','Super Admin'))->get();
            return $this->successResponse(UserResource::collection($users), 'All Users', 200);
        } catch (\Throwable $th) {
            return $this->unknownResponse($th);
        }
    }
    public function store(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', 400, ['errors' => $validator->errors()]);
            }
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            $user->assignRole('Member');
            return $this->successResponse(new UserResource($user), 'Successfully add user', 201);
        } catch (\Throwable $th) {
            return $this->unknownResponse($th);
        }
    }
    public function show(User $user){
        try {
            return $this->successResponse(new UserResource($user), 'Get User', 200);
        } catch (\Throwable $th) {
            return $this->unknownResponse($th);
        }
    }
    public function update(Request $request, User $user){
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', 400, ['errors' => $validator->errors()]);
            }
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            return $this->successResponse(new UserResource($user), 'Successfully edit user', 200);
        } catch (\Throwable $th) {
            return $this->unknownResponse($th);
        }
    }
    public function destroy(User $user){
        try {
            $user->delete();
            return $this->successResponse([], 'Succesfully delete User', 200);
        } catch (\Throwable $th) {
            return $this->unknownResponse($th);
        }
    }
}
