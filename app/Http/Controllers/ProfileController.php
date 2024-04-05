<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function getUserProfileData(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            return response()->json([
                "success" => true,
                "data" => $user
            ]);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Hibás adatok"
            ], 404);
        }
    }

    public function setUserProfileData(Request $request)
    {
        $messages = [
            'city_id.required' => 'Város megadása kötelező',
        ];

        $validator = validator($request->all(), [
            'city_id' => 'required|integer',
        ], $messages);

        if ($validator->fails()) {
            $this->failedValidation($validator);
        }

        $user = Auth::user();

        $user->city_id = $request->city_id;
        $user->save();

        return response()->json([
            "message" => "Város frissítve",
            "success" => true
        ], 200);
    }

    public function setNewPassword(Request $request)
    {
        $messages = [
            "password.required" => "Jelszó megadása kötelező",
        ];

        $validator = validator($request->all(), [
            'password' => ["required"]
        ], $messages);

        if ($validator->fails()) {
            $this->failedValidation($validator);
        }

        $user = Auth::user();

        $user->password = $request->password;
        $user->save();

        return response()->json([
            "message" => "Jelszó frissítve",
            "success" => true
        ], 200);
    }

    public function deleteAccount()
    {
        $user = Auth::user();

        if ($user) {
            $user->delete();

            return response()->json([
                "success" => true,
            ]);
        } else {
            return response()->json([
                "success" => false,
            ], 404);
        }
    }
}
