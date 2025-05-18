<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpoTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->expo_token = $request->token;
        $user->save();

        return response()->json(['message' => 'Token actualizado']);
    }
}
