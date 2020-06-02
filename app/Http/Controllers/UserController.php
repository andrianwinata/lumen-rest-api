<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        // Validate User Input
        $this->validate($request, [
            'email' => 'required|unique:users|email',
            'password' => 'required|min:8'
        ]);

        // Get User Input
        $email = $request->input('email');
        $password = $request->input('password');
        $hashPassword = Hash::make($password);

        // Create User
        $user = User::create([
            'email' => $email,
            'password' => $hashPassword
        ]);

        return response()->json(['message' => 'Success'], 201);

    }

    public function login(Request $request)
    {
        // Validate User Input
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        // Get User Input
        $email = $request->input('email');
        $password = $request->input('password');

        // Check Email In Database
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['message' => 'Login Failed'], 401);
        }

        // Check Password In Database Based On Email Above
        $isPasswordValid = Hash::check($password, $user->password);
        if (!$isPasswordValid) {
            return response()->json(['message' => 'Login Failed'], 401);
        }

        // Generate Token For User
        $generateToken = bin2hex(random_bytes(40));

        // Update User Token In Database
        $user->update([
            'token' => $generateToken
        ]);

        // Return User Data (Username And Token)
        return response()->json($user);
    }
}
