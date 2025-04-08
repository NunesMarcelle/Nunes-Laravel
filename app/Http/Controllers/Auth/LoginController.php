<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login.index'); // resources/views/auth/login.blade.php
    }

    public function login(Request $request)
    {
        // Validação básica
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Verifica se as credenciais são válidas
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // Previne ataques de sessão
            return redirect()->intended('/dashboard'); // Redireciona para o destino pretendido
        }

        return back()->withErrors([
            'email' => 'As credenciais informadas estão incorretas.',
        ])->withInput(); // Retorna com erro e mantém o valor dos campos
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
