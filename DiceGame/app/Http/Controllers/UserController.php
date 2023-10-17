<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

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
     *     "User data": {
     *         "id": 1,
     *         "nickname": "userNickname",
     *         "email": "user@mail.com"
     *     },
     *     "token": "User Token"
     * }
     * @response 422 {
     *     "message": "Different Laravel validation messages from lang/en/validation.php",
     * }
     * @response 400 {
     *     "message": "User has not been created",
     * }
     */
    public function store(Request $request)
    {
        $validationRules = [
            'nickname' => 'nullable|min:4|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => [
                'required', 'confirmed', Password::min(8)
                // Uncomment this validations rules if is wished
                //->letters()->mixedCase()->numbers()->symbols()
            ],
        ];
        // Validate request inputs
        $requestData = $request->all();
        $validator = validator($requestData, $validationRules);

        if ($validator->fails()) {
            // Get a 422 response with validation errors
            return response()->json(
                ['message' => 'Validation failed', 'errors' => $validator->errors()],
                422
            );
        }
        $requestData['password'] = Hash::make($requestData['password']);

        if ($user = User::create($requestData)) // TODO asign role
        {
            // If creations works
            $Authuser = Auth::user();
            /** @var \App\Models\User $user **/
            $token = $user->createToken('User_Token')->accessToken;
            // Show "Anonymous" if nickname is null
            $user->nickname = $user->nickname ?? 'Anonymous';
            return response()->json(
                ['message' => 'User created successfully', 'User data' => $user, 'token' => $token,],
                201
            );
        };

        // If creations fails
        return response()->json(
            ['error' => 'User has not been created'],
            400
        );
    }
    /**
     * LOGIN AN EXISTING USER.
     *
     * @group Users
     * @bodyParam email. The email of the user. Example: user@mail.com
     * @bodyParam password. The password of the user. Example: secret_password
     * @bodyParam password_confirmation. It must match with password
     *
     * @response 200 {
     *     "message": "User token successfully created'",
     *     "token": "User Token"
     *      "User data": {
     *         "id": 1,
     *         "nickname": "userNickname",
     *         "email": "user@mail.com"
     *     }
     * }
     * @response 422 {
     *     "message": "Different Laravel validation messages from lang/en/validation.php",
     * }
     * @response 401 {
     *     "message": "Unouthorized",
     * }
     */
    public function login(Request $request)
    {
        $validationRules = [
            'email' => 'required|email|max:255',
            'password' => [
                'required', Password::min(8)
                // Uncomment this validations rules if wished
                //->letters()->mixedCase()->numbers()->symbols()
            ],
        ];
        // Validate request inputs
        $requestData = $request->only('email', 'password');
        $validator = validator($requestData, $validationRules);

        if ($validator->fails()) {
            // Get a 422 response with validation errors
            return response()->json(
                ['message' => 'Validation failed', 'errors' => $validator->errors()],
                422
            );
        }
        if (!Auth::attempt($requestData)) {
            return response()->json(
                [
                    'message' => 'Unauthorized'
                ],
                401
            );
        }

        $user = Auth::user();
        /** @var \App\Models\User $user **/
        $token = $user->createToken('User_Token')->accessToken;

        return response()->json(
            ['message' => 'User token successfully created', 'token' => $token, 'User data' => $user],
            200
        );
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
     * @response 404 {
     *     "error": "User not found"
     * }
     * @response 422 {
     *     "message": "Different Laravel validation messages from lang/en/validation.php",
     */
    public function update(Request $request, string $id)
    {
        $validationRules = [
            'nickname' => 'nullable|min:4|max:255|unique:users,nickname,'
                // Uncomment this validations if update this fields is wanted
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
        $validator = validator($request->all(), $validationRules);

        if ($validator->fails()) {
            // Get a 422 response with validation errors
            return response()->json(
                ['message' => 'Validation failed', 'errors' => $validator->errors()],
                422
            );
        }

        // Get user nickname
        $user = User::find($id);

        if (!$user) {
            return response()->json(
                ['error' => 'User not found'],
                404
            );
        }

        $user->nickname = $request->input('nickname');

        $user->update();
        // If creations works
        // Asign "Anonymous" if nickname is null
        $user->nickname = $user->nickname ?? 'Anonymous';

        return response()->json(
            ['message' => 'User successfully updated', 'data' => $user],
            200
        );
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
            return response()->json(
                ['error' => 'User not found'],
                404
            );
        }
        // User is found
        $authUser = Auth::user();
        if ($authUser->id == $id) {
            // Asign "Anonymous" if nickname is null
            $user->nickname = $user->nickname ?? 'Anonymous';
            return response()->json(
                ['message' => 'User found', 'data' => $user],
                200
            );
        }
        return response()->json(
            ['error' => 'Unauthorized'],
            401
        );
    }

    /**
     * REMOVE A SPECIFIC USER BY ID.
     *
     * @group Users
     * @urlParam id required The ID of the user.
     *
     * @response 200 {
     *     "message": "User successfully deleted",
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
            return response()->json(
                ['error' => 'User not found'],
                404
            );
        }
        $user->delete();
        return response()->json(
            ['message' => 'User successfully deleted'],
            200
        );
    }
    /**
     * LOGOUT AN EXISTING USER.
     *
     * @group Users
     *
     * @response 200 {
     *    "message": "User successfully logged out"
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(
            ['message' => 'User successfully logged out'],
            200
        );
    }
}
