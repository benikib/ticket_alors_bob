<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    
public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name'     => 'required|string|max:255',
        'email'    => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed',
        'role'     => 'required|string|in:user,admin', // ajuste selon tes besoins
    ]);

    if ($validator->fails()) {
        return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
    }

    $user = User::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'password' => Hash::make($request->password),
        'role'     => $request->role,
    ]);

    Auth::login($user);

    return response()->json([
        'status' => true,
        'message' => 'Utilisateur inscrit avec succès.',
        'user' => $user
    ]);
}

    public function login()
    {


    
       return view('auth.login');
    }

    public function toLogin(Request $request)
    {
     
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->route('billet.index');
        }
    }


    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->route('login');
    }

   
    public function profile(Request $request)
    {
        return response()->json(['status' => true, 'user' => Auth::user()]);
    }
}
