<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string', 
            'access_code' => 'required|string'
        ]);

        $tester = Tester::where('code', $data['code'])->first();
        if (! $tester || ! Hash::check($data['access_code'], $tester->access_code)) {
            throw ValidationException::withMessages(['code' => 'Invalid tester ID or access code.']);
        }

        return [
            'token'  => $tester->createToken('pilot', [$tester->role])->plainTextToken,
            'tester' => $tester->only('id', 'code', 'name', 'lgu', 'role'),
        ];
    }
}
