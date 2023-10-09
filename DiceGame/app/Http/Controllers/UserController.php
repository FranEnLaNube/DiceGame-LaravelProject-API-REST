<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    // TODO add parameters required at the block quote in bodyParam sections

    /**
     * Store a new user.
     *
     * @group Users
     * @bodyParam nickname. The nickname of the user. Example: userNickname
     * @bodyParam email. The email of the user. Example: user@mail.com
     * @bodyParam password. The password of the user. Example: secret_password
     *
     * @response 201 {
     *     "message": "User created successfully",
     *     "data": {
     *         "id": 1,
     *         "nickname": "userNickname",
     *         "email": "user@mail.com"
     *     }
     * }
     * @response 400 {
     *     "message": "This email is already registered",
     *     "message": "This nickname is already taken"
     * }
     */
    public function store(Request $request)
    {
        //TODO Add validations
        // Check if user is already created
        $existingUser = User::where('email', $request->email)->first();

        //TODO Pensar la lógica para el usuario anónimo
        //$name = $request->filled('nickname') ? $request->name : 'anonymous';

        // Get user nickname
        $existingUserNickname = User::where('nickname', $request->nickname)->first();

        if ($existingUser /* != null */) { // FIXME clean this

            return response()->json(['message' => 'This email is already registered'], 400);
        } else if ($existingUserNickname /* != null */) { // FIXME clean this

            return response()->json(['message' => 'This nickname is already taken'], 400);
        }
        if ($user = User::create([
            'nickname' => $request->nickname,
            'email' => $request->email,
            'password' => $request->password, //TODO add encryptation
            //TODO add pass double confirmation
        ])) // TODO asign role
        {
            // If creations works
            return response()->json(['message' => 'User created successfully', 'data' => $user], 201);
        };

        // TODO  add token
        /* $token = $user->createToken('Personal Access Token')-> accessToken;
        return response()->json(['token'=>$token],201); */


        // If creations fails
        return response()->json(['error' => 'User has not been created'], 400);
    }

    /**
     * Get a specific user by ID.
     *
     * @group Users
     * @urlParam id required The ID of the user.
     *
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "nickname": "userNickname",
     *         "email": "user@mail.com"
     *     }
     * }
     * @response 404 {
     *     "error": "User not found"
     * }
     */
    public function show(string $id)
    {
        $user = User::find($id);
        // If user is not found
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        // User is found
        return response()->json(['message' => 'User found', 'data' => $user], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }
    // TODO Add the rest of atributes to edit
    /**
     * Update a specific user.
     *
     * @group Users
     * @urlParam id required The ID of the user.
     * @bodyParam nickname. The nickname of the user. Example: userNickname
     *
     * @response 200 {
     *     "message": "User successfully updated",
     *     "data": {
     *         "id": 1,
     *         "nickname": "userNickname",
     *         "email": "user@mail.com"
     *     }
     * }
     * * @response 404 {
     *     "error": "User not found"
     * }
     */
    public function update(Request $request, string $id)
    {

        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // TODO Add validations
        if ($request->has('nickname')) {
            User::find($id)->update([
                'nickname' =>  $request->nickname
            ]);
        }

        return response()->json(['message' => 'User successfully updated', 'data' => $user], 200);
    }

    /**
     * Remove a specific user by ID.
     *
     * @group Users
     * @urlParam id required The ID of the user.
     *
     * @response 200 {
     *     "message": "User successfully updated",
     *     }
     * }
     * @response 404 {
     *     "error": "User not found"
     * }
     */    public function destroy(string $id)
    {
        //This method is not asked at the project
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User successfully deleted'], 200);
    }
}
