<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $messages = [
            'name.required' => 'Név megadása kötelező',
            'username.required' => 'Felhasználónév megadása kötelező',
            'email.required' => 'E-mail megadása kötelező',
            "password.required" => "Jelszó megadása kötelező",
            'borndate.required' => 'Születési dátum megadása kötelező',
            'city_id.required' => 'Város megadása kötelező',
        ];

        $validator = validator($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users',
            'email' => 'required|string|email|unique:users',
            "password" => ["required"],
            "password_confirmation" => ["required"],
            'borndate' => 'required|date',
            'city_id' => 'required|integer',
        ], $messages);

        if ($validator->fails()) {
            throw new HttpResponseException(response()->json([
                "success" => false,
                "message" => "Adatbeviteli hiba",
                "data" => $validator->errors()
            ], 422));
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        $user = User::create($input);

        $success['name'] = $user->name;

        return response()->json([
            "message" => "A regisztrációs sikeres",
            "success" => $success
        ], 201);

    }

    public function login(Request $request)
    {
        $messages = [
            'username.required' => 'Felhasználónév megadása szükséges',
            'password.required' => 'Jelszó megadása szükséges',
        ];

        $validator = validator($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ], $messages);

        if ($validator->fails()) {
            $this->failedValidation($validator);
        }

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $authUser = Auth::user();
            $success['username'] = $authUser->username;
            $success['token'] = $authUser->createToken($authUser->username . "token")->plainTextToken;

            return response()->json([
                "message" => "Az azonosítás sikeres",
                "success" => $success
            ]);
        }
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            "success" => false,
            "message" => "Hiba történt az adatok bevitele során",
            "data" => $validator->errors()
        ], 422));
    }


    public function logout(Request $request)
    {
        auth("sanctum")->user()->currentAccessToken()->delete();
        return response()->json(["message" => "Sikeres kijelentkezés"]);
    }
}
