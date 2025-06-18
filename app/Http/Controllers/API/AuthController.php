<?php
namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function register(Request $request) {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);
                $token = $user->createToken('api_token')->plainTextToken;


 return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);    }

    public function login(Request $request) {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        // return response()->json(['token' => $token]);
 return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);    
    }

    public function logout(Request $request) {
        // $request->user()->tokens()->delete();
            $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}

