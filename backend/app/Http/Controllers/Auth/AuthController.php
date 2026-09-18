<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Unauthenticated;

#[Group('Autenticação', 'Login, logout e dados do usuário autenticado. Todas as rotas deste grupo exigem Bearer token, exceto login.')]
class AuthController extends Controller
{
    /**
     * Login
     *
     * Autentica com email e senha e retorna um token de acesso (Bearer) a
     * ser enviado no header `Authorization` das demais rotas da API.
     */
    #[Unauthenticated]
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user->only(['id', 'name', 'email']),
            'token' => $token,
        ]);
    }

    /**
     * Logout
     *
     * Revoga o token de acesso usado na requisição.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }

    /**
     * Usuário autenticado
     *
     * Retorna os dados básicos (id, nome, e-mail) do usuário dono do token
     * usado na requisição.
     */
    public function me(Request $request)
    {
        return response()->json($request->user()->only(['id', 'name', 'email']));
    }
}
