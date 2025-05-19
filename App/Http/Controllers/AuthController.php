<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Human;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $human = Human::where('email', $request->email)->first();

        if (!$human || !Hash::check($request->password, $human->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $human->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
}
