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
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Por favor, insira um e-mail válido.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }

        // Credenciais
        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        // Tentativa de autenticação
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Redireciona baseado no role do usuário
            $user = Auth::user();
            
            return redirect()->intended(route('home.index'))->with('success', 'Login realizado com sucesso!');
        }

        // Se falhar, retorna com erro
        return back()
            ->withInput($request->only('email'))
            ->with('error', 'Credenciais inválidas. Verifique seu e-mail e senha.');
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
