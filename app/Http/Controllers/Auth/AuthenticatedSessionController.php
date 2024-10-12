<?php

namespace App\Http\Controllers\Auth;

use App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AuthenticatedSessionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);
        if (!Auth::attempt($request->only('email', 'password')))
            return response()->json(['message' => 'Invalid login credentials'], 401);
        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;
        $lang = $user->Profile->language;
        App::setLocale($lang);
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'status' => 'Login successful',
            'lang' => $lang,
        ]);
    }

    public function destroy(Request $request)
    {
        if ($request->is('api/*')) {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'message' => __('auth.logout'),
            ], 200);
        } else {
            Auth::logout();
            return redirect(route('login'));
        }
    }
}
