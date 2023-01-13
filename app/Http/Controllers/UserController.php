<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Faker\Provider\hy_AM\PhoneNumber;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Registration new user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(UserRequest $request ){
        $newUser = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'phone' => $request['phone'],
            'slug' => SlugService::createSlug(User::class, 'slug', $request['name']),
        ]);
        $newUser->save();
        Auth::login($newUser);
        $token = $newUser->createToken($request["email"]);
        $newUser["api_token"] = $token->plainTextToken;
        $data = [
            'success' => true,
            'data' => $newUser,
        ];
        return
            response($data)->setStatusCode(200)->header('Status-Code', '200');
    }

    /**
     * Login User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
        $validator = Validator::make($request->all(), ['email' => 'required|email', 'password' => 'required']);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                "errors" => $validator->errors()
            ]);
        }
        $validUser = Auth::attempt(['email' => $request["email"], 'password' => $request["password"]]);
        if ($validUser) {
            $user = Auth::getProvider()->retrieveByCredentials(['email' => $request["email"], 'password' => $request["password"]]);
            Auth::login($user);
            $user["api_token"] = $user->createToken($request["email"], ['server:update'])->plainTextToken;
            $data = [
                'success' => true,
                'data' => $user
            ];
            return response()->json($data)
                ->setStatusCode(200)->header('Status-Code', '200');

        } else {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401)->header('Status-Code', '401'));
        }
    }

    public function logout(){
        Auth::user()->tokens()->where('id', Auth::id())->delete();
        $user = Auth()->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        return response()->json([   'success' => true,
            'message' => 'User successfully logged out'])->header('Status-Code', '200');
    }
}
