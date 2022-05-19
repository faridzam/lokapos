<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\appController;

use App\Models\pos_cashier_desktop;
use App\Models\pos_store_desktop;
use App\Models\pos_log_activity_desktop;


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

    public function index(){

        if (Auth::guard('cashier')->check()) {
            return redirect('/app');
        }

        return view('login');

    }

    public function authenticate(Request $request)
    {

        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if(Auth::guard('cashier')->attempt($credentials)){
            $request->session()->regenerate();

            pos_log_activity_desktop::create([
                'pic' => $request->username,
                'type' => 5,
                'note' => $request->username." memasuki sistem POS",
            ]);

            // //Instantiate other controller class in this controller's method
            // $pickStore = new appController;
            // //Use other controller's method in this controller's method
            // $pickStore->pickStore($request->header('User-Agent'));

            //return redirect()->intended('deposit');

            return redirect('/pick-store/'.$request->header('User-Agent'));
            //return redirect('pick-store/1');
        }

        session()->flash('error', 'login gagal!');
        return back();

    }
}
