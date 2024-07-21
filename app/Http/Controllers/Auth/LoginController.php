<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laracasts\Flash\Flash;
use Symfony\Contracts\Service\Attribute\Required;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request){

                $input = $request->all();

                //dd($input);

                $this -> validate
                ($request, 
                ['email' => 'required', 'password' => 'required']);

                $credenciales = [
                    'email' => $input['email'],
                    'password' =>$input['password']
                ];

                //otra manera de validar
                // $user = DB :: table ('users') -> where ('email',$input['email']) -> first();

                // if (!empty($user) && $user -> email == $input['email']){
                //     Flash :: error ('El usuario con el correo no existe ');
                //     return redirect('/login')-> withInput();               
                //  }


                //  $pass = Hash :: make($input ['password']);
                //  if ($user -> passwoord != $pass){
                //     Flash :: error ('La contraseña es incorrecta ');
                //     return redirect('/login')-> withInput();               
                //  }
                 

            if(Auth::attempt($credenciales)) {
                return redirect() -> to ($this ->redirectTo);
            }  else {
                Flash ::info ('El userName no es correcto, favor intentar nuevamente');
                return redirect('/login')-> withInput();
            }
    }


  
}
