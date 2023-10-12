<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    // TODO add parameters required at the block quote in bodyParam sections

    /**
     * REGISTER AND STORE A NEW USER.
     *
     * @group Users
     * @bodyParam nickname. The nickname of the user. Example: userNickname
     * @bodyParam email. The email of the user. Example: user@mail.com
     * @bodyParam password. The password of the user. Example: secret_password
     * @bodyParam password_confirmation. It must match with password
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
     *     "message": "Different Laravel validation messages from lang/en/validation.php",
     * }
     */
    public function register(Request $request)
    {
        // Define validation rules
        $rules = [
            'nickname' => 'nullable|unique:users',
            'email' => 'required|email|unique:users',
            'password' => [
                'required', 'confirmed', Password::min(8)
                //->letters()
                //->mixedCase()
                //->numbers()
                //->symbols()
                // TODO uncomment this validations rules if it's wished
            ],
        ];
        // Validate request inputs
        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            // Get a 422 response with validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }
        if ($user = User::create($request->input())) // TODO asign role
        {
            // If creations works
            // Asign "Anonymous" if nickname is null
            $user->nickname = $user->nickname ?? 'Anonymous';
            return response()->json(['message' => 'User created successfully', 'data' => $user], 201);
        };

        // TODO  add token
        /* $token = $user->createToken('Personal Access Token')-> accessToken;
        return response()->json(['token'=>$token],201); */


        // If creations fails
        return response()->json(['error' => 'User has not been created'], 400);
        // TODO Add the rest of atributes to edit
    }
    /**
     * UPDATE A SPECIFIC USER.
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
        // Define validation rules
        $rules = [
            'nickname' => 'nullable|unique:users,nickname,'
                // TODO uncomment this validations if update this fields is wanted
                /*,'email' => 'required|email|unique:users',
                'password' => [
                current_password:api',
                required', 'confirmed', Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols() ] */
                . $id, // To ignore user field values in validation
        ];

        // Validate request inputs
        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            // Get a 422 response with validation errors
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        //TODO Pensar la lógica para el usuario anónimo
        //$name = $request->filled('nickname') ? $request->name : 'anonymous';
        // Get user nickname
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->nickname = $request->input('nickname');

        $user->update();
        // If creations works
        // Asign "Anonymous" if nickname is null
        $user->nickname = $user->nickname ?? 'Anonymous';

        return response()->json(['message' => 'User successfully updated', 'data' => $user], 200);
    }

    /**
     * GET A SPECIFIC USER BY ID.
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
        // Asign "Anonymous" if nickname is null
        $user->nickname = $user->nickname ?? 'Anonymous';
        return response()->json(['message' => 'User found', 'data' => $user], 200);
    }

    /**
     * REMOVE A SPECIFIC USER BY ID.
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
        //TODO This method is not asked at the project
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User successfully deleted'], 200);
    }
}
