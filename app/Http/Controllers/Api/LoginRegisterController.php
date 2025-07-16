<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class LoginRegisterController extends Controller
{
    use ApiResponseTrait;

    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email:rfc,dns|unique:users,email|max:250',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation Error!', 422, $validator->errors());
        }

        $user = User::create($request->all());

        $data['token'] = $user->createToken($request->email)->plainTextToken;
        $data['user'] = $user;

        return $this->successResponse($data, 'User is registered successfully', 201);
    }

    /**
     * Authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation Error!', 422, $validator->errors());
        }

        // Check email exist
        $user = User::where('email', $request->email)->first();

        // Check password
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        $data['token'] = $user->createToken($request->email)->plainTextToken;
        $data['user'] = $user;

        return $this->successResponse($data, 'Login successful');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logged out successfully');
    }
}