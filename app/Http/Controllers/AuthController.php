<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Mostra a tela de login
     */
    public function index()
    {
        // Se já estiver autenticado, redireciona para home
        if (Auth::check()) {
            return redirect()->route('home.index');
        }

        return view('auth.login');
    }

    /**
     * Processa o login
     */
    public function store(Request $request)
    {
        // Validação dos dados
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string|min:6',
        ], [
            'username.required' => 'O campo nome de usuário é obrigatório.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('username'));
        }

        // Busca o usuário pelo username
        $user = \App\Models\User::where('username', $request->username)->first();

        // Verifica se o usuário existe e se a senha está correta
        if ($user && \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            Auth::login($user, $request->filled('remember'));
            $request->session()->regenerate();

            return redirect()->intended(route('home.index'))->with('success', 'Login realizado com sucesso!');
        }

        // Se falhar, retorna com erro
        return back()
            ->withInput($request->only('username'))
            ->with('error', 'Credenciais inválidas. Verifique seu nome de usuário e senha.');
    }

    /**
     * Faz logout do usuário
     */
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.index')->with('success', 'Logout realizado com sucesso!');
    }
}
