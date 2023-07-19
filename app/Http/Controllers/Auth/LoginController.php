<?php

namespace App\Http\Controllers\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    }

    public function login(Request $request)
    {
        // Validar los datos enviados en la solicitud
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Intentar autenticar al usuario con las credenciales proporcionadas
        if (Auth::attempt($request->only('email', 'password'))) {
            // Si las credenciales son válidas, redireccionar al destino previsto (generalmente la página de inicio)
            return redirect()->intended($this->redirectTo);
        } else {
            // Si las credenciales son inválidas, lanzar una excepción de validación con un mensaje de error
            throw ValidationException::withMessages([
                'login_error' => 'Credenciales inválidas o no existe el registro.',
            ])->redirectTo(route('login'));
        }
    }
}


