<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityCollection;
use App\Http\Resources\IncidentCollection;
use App\Http\Resources\ProjectCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;
use App\Http\Resources\UserCollection;

class UserController extends Controller
{

    /**
     * Login users and create a token
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * */
    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        return response()->json(compact('token'));
    }

    /**
     * Register a new user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * */

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);


        return response()->json(compact('user'), 201);
    }

    /**
     * Delete a user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(User $user)
    {
        $user->delete();
        return response()->json(compact('user'));
    }

    /**
     * Get all users
     * @param Request $request
     * @return \App\Http\Resources\UserCollection
     */

    public function show()
    {
        return new UserCollection(User::all());
    }

    /**
     * Get all projects from a user
     * @param User $user
     * @return \App\Http\Resources\ProjectCollection
     */
    public function showProjects(User $user)
    {
        return new ProjectCollection($user->projects);
    }

    /**
     * Get all activities from a user
     * @param User $user
     * @return \App\Http\Resources\ActivityCollection
     * */

    public function showActivities(User $user)
    {
        return new ActivityCollection($user->activities);
    }

    /**
     * Get all incidents from a user
     * @param User $user
     * @return \App\Http\Resources\IncidentCollection
     * */
    public function showIncidents(User $user)
    {
        return new IncidentCollection($user->incidents);
    }
}
