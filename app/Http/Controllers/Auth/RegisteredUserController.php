<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'company_name' => ['required', 'string', 'max:255'],
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);

        $imagePath = null;

        if ($request->hasFile('img')) {
            $imagePath = $request->file('img')->store('users', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_name' => $request->company_name,
            'img' => $imagePath,
        ]);

        $user->id_conta = $user->id;
        $user->save();

        event(new Registered($user));

        Auth::login($user);

        // Redireciona para a dashboard com mensagem de sucesso
        return redirect()->route('login')->with('success', 'Cadastro realizado com sucesso!');
    }

}
