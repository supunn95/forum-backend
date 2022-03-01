<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\V1\BaseController as BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ApiAuthController extends BaseController
{

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        try {

            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);

            //attach role to created user
            $role = config('roles.models.role')::where('name', '=', 'User')->first(); //choose the default role upon user creation.
            $user->attachRole($role);

            return $this->sendResponse('success', 'User register successfully.');

        } catch (Throwable $e) {

            return $this->sendError('Error.', $e);
        }

    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user()->load('roles');
            $success['token'] = $user->createToken('userToken')->accessToken;
            $success['id'] = $user->id;
            $success['role'] = $user->roles->first()->slug;

            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('error.', ['error' => 'The provided credentials are incorrect.'], 422);
        }
    }

    /**
     * Logout api
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $user = Auth::user()->token();
        $user->revoke();
        return $this->sendResponse('success', 'User logged out successfully.');
    }

}
