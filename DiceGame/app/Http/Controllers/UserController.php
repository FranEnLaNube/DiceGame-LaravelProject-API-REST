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
     * STORE()
     *
     * REGISTER AND STORE A NEW USER.
     *
     * @group User
     *
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
     *     "message": ""Validation failed",
     *      "field": [
     *          "The field must be....."
     *      ],
     *    }
     * }     * @response 500 {
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

        if ($user = User::create($requestData)) {
            // If creations works
            /** @var \App\Models\User $user **/

            // Assign 'admin' scope if the user's email is the same as saved in .env
            $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
            $scope = ($user->email === $adminEmail) ? 'admin' : 'player';

            $token = $user->createToken('User_Token', [$scope])->accessToken;

            // Show "Anonymous" if nickname is null
            $user->nickname = $user->nickname ?? 'Anonymous';
            return response()->json(
                ['message' => 'User created successfully', 'User data' => $user, 'token' => $token,],
                201
            );
        };
        // If creations fails for a Internal Server Error
        return response()->json(
            ['error' => 'User has not been created'],
            500
        );
    }
    /**
     * LOGIN()
     *
     * LOGIN AN EXISTING USER.
     *
     * @group User
     * @bodyParam email. The email of the user. Example: user@mail.com
     * @bodyParam password. The password of the user. Example: secret_password
     * @bodyParam password_confirmation. It must match with password
     *
     * @response 422 {
     *     "message": ""Validation failed",
     *      "field": [
     *          "The field must be....."
     *      ],
     *    }
     * }
     * @response 403 {
     *     "message": "Email or password is incorrect, please try again.",
     * }
     * @response 200 {
     *     "message": "User token successfully created'",
     *     "token": "User Token"
     *      "User data": {
     *         "id": 1,
     *         "nickname": "userNickname",
     *         "email": "user@mail.com"
     *     }
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
        // Check if $requestData['email'] and $requestData['password'] matches with database data
        if (!Auth::attempt($requestData)) {
            return response()->json(
                [
                    'message' => 'Email or password is incorrect, please try again.',
                ],
                403
            );
        }
        $user = Auth::user();
        /** @var \App\Models\User $user **/
        // Asigns scope to user
        // Assign 'admin' scope if the user's email is the same as saved in .env
        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
        $scope = ($user->email === $adminEmail) ? 'admin' : 'player';

        $token = $user->createToken('User_Token', [$scope])->accessToken;

        return response()->json(
            ['message' => 'User token successfully created', 'token' => $token, 'User data' => $user],
            200
        );
    }
    /**
     * UPDATE()
     *
     * UPDATE A SPECIFIC USER.
     *
     * @group Player
     *
     * @urlParam id required The ID of the user.
     * @bodyParam nickname. The nickname of the user. Example: userNickname
     *
     * @response 403 {
     *     "error": "Hey hey hey! You cannot do this! Get out of here",
     * }
     * @response 403 {
     *     "error": "Unauthorized, this is not your account!",
     * }
     * @response 422 {
     *     "error": "User not found"
     * }
     * @response 422 {
     *     "message": ""Validation failed",
     *      "field": [
     *          "The field must be....."
     *      ],
     *    }
     * }
     * @response 200 {
     *     "message": "User successfully updated",
     *     "data": {
     *         "id": 1,
     *         "nickname": "userNickname",
     *         "email": "user@mail.com"
     *     }
     * }
     */
    public function update(Request $request, string $id)
    {
        // Get user
        $user = User::find($id);
        if (!$user) {
            return response()->json(
                ['message' => 'User not found'],
                422
            );
        }
        // Check if the user which is requesting can do this action by its scope
        if (!$request->user()->tokenCan('player')) {
            return response()->json(
                [
                    'error' => 'Hey hey hey! You cannot do this! Get out of here'
                ],
                403
            );
        }
        // Check if user which is requesting is the same as the authenticated with token
        $authUser = Auth::user();
        if ($authUser->id != $id) {
            return response()->json(
                ['error' => 'Unauthorized, this is not your account!'],
                403
            );
        }
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
        // Update user nickname
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
     * SHOW()
     *
     * GET A SPECIFIC USER BY ID.
     *
     * @group Player
     *
     * @urlParam id required The ID of the user.
     *
     * @response 403 {
     *     "error": "Hey hey hey! You cannot do this! Get out of here",
     * }
     * @response 403 {
     *     "error": "Unauthorized, this is not your account!",
     * }
     * @response 422 {
     *     "error": "User not found"
     * }
     * @response 200 {
     *     "data": {
     *         "id": 1,
     *         "nickname": "userNickname",
     *         "email": "user@mail.com"
     *     }
     * }
     */
    public function show(Request $request, string $id)
    {
        //This method is not asked at the project
        $user = User::find($id);
        // If user is not found
        if (!$user) {
            return response()->json(
                ['error' => 'User not found'],
                404
            );
        }
        // User is found
        // Check if the user which is requesting can do this action by its scope
        if (!$request->user()->tokenCan('player')) {
            return response()->json(
                [
                    'error' => 'Hey hey hey! You cannot do this! Get out of here'
                ],
                403
            );
        }
        // Check if user which is requesting is the same as the authenticated with token
        $authUser = Auth::user();
        if ($authUser->id != $id) {
            return response()->json(
                ['error' => 'Unauthorized, this is not your account!'],
                403
            );
        }
        // Asign "Anonymous" if nickname is null
        $user->nickname = $user->nickname ?? 'Anonymous';
        return response()->json(
            ['message' => 'User found', 'data' => $user],
            200
        );
    }
    /**
     * DESTROY()
     *
     * REMOVE A SPECIFIC USER BY ID.
     *
     * @group Player
     * @urlParam id required The ID of the user.
     *
     * @response 403 {
     *     "error": "Hey hey hey! You cannot do this! Get out of here",
     * }
     * @response 403 {
     *     "error": "Unauthorized, this is not your account!",
     * }
     * @response 422 {
     *     "error": "User not found"
     * }
     * @response 200 {
     *     "message": "User successfully deleted",
     *     }
     * }
     */
    public function destroy(Request $request, string $id)
    {
        //This method is not asked at the project

        $user = User::find($id);
        if (!$user) {
            return response()->json(
                ['error' => 'User not found'],
                404
            );
        }
        // Check if the user which is requesting can do this action by its scope
        if (!$request->user()->tokenCan('player')) {
            return response()->json(
                [
                    'error' => 'Hey hey hey! You cannot do this! Get out of here'
                ],
                403
            );
        }
        // Check if user which is requesting is the same as the authenticated with token
        $authUser = Auth::user();
        if ($authUser->id != $id) {
            return response()->json(
                ['error' => 'Unauthorized, this is not your account!'],
                403
            );
        }
        $user->delete();
        return response()->json(
            ['message' => 'User successfully deleted'],
            200
        );
    }
    /**
     * LOGOUT()
     *
     * LOGOUT AN EXISTING USER.
     *
     * @group User
     *
     * }
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
