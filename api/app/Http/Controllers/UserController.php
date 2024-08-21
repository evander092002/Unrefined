<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthenticateUserRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\AuthenticatedUserResource;
use App\Http\Resources\UserCreateResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserUpdateResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index() 
    {
        $users = User::all();

        if ($users->count() > 0) {
            return response()->json(['users' => $users]);
        } else {
            return response()->json([
                'users' => 'No Users Found'
            ]);
        }
    }
    
    public function updateUser(Request $request, User $user)
{
    // Validate the incoming request data
    $validatedData = $request->validate([
        'first_name' => 'sometimes|required|string|max:50',
        'last_name' => 'sometimes|required|string|max:50',
        'middle_initial' => 'sometimes|nullable|string|size:1',
        'gender' => 'sometimes|required|in:male,female',
        'phone_no' => 'sometimes|required|string|max:255',
        'status' => 'sometimes|required|in:active,inactive',
        'role' => 'sometimes|required|in:admin,client,superadmin',
        'username' => 'sometimes|required|string|max:50',
        'password' => 'sometimes|required|string|max:255',
    ]);

    // Update the user record
    $user->update($validatedData);

    // Optionally, return a JSON response
    return response()->json([
        'message' => 'User updated successfully',
        'user' => $user
    ]);
}


    public function show($id) 
    {
        $user = User::find($id);

        if ($user) {
            return response()->json([
                'admin' => $user
            ]);
        } else {
            return response()->json([
                'message' => 'Admin Not Found'
            ]);
        }
    }

    public function update(UserUpdateRequest $request, $id): JsonResponse
    {
        // Find the user by ID
        $user = User::find($id);

        // Check if user exists
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Get only the validated data from the request
        $data = $request->validated();

        // Update the user with only the provided data
        $user->update($data);

        return response()->json([
            'data' => new UserUpdateResource($user)
        ]);
    }

    public function create(CreateUserRequest $payload) 
    {
        $first_name = $payload->first_name;
        $last_name = $payload->last_name;
        $middle_initial = $payload->middle_initial;
        $gender = $payload->gender;
        $phone_no = $payload->phone_no;
        $status = $payload->status;
        $role = $payload->role;
        $username = $payload->username;
        $password = $payload->password;

        $user = User::create([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'middle_initial' => $middle_initial,
            'gender' => $gender,
            'phone_no' => $phone_no,
            'status' => $status,
            'role' => $role,
            'username' => $username,
            'password' => bcrypt($password)
        ]);

        return new UserCreateResource($user);
    }


    //Login
    public function login(AuthenticateUserRequest $payload){
        $username = $payload->username; 
        $password = $payload->password;

        $user = User::where('username', $username)->first();

        if(!$user || !Hash::check($password, $user->password)){
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect'],
            ]);
        }

        $response = (object) [
            'user' => new UserResource($user),
            'token' => $user->createToken('auth-token')->plainTextToken
        ];

        return new AuthenticatedUserResource($response);
    }

    public function logout(Request $payload){
        $taker = $payload->user();
        $taker->tokens()->delete();
        return "logout success";
    }
}
