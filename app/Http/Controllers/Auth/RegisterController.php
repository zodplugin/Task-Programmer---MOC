<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\VerificationCode;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = 'dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'no_telp' => ['required', 'string', 'max:15','unique:users'],
            'provinsi' => ['required', 'string'],
            'kota' => ['required', 'string'],
            'kecamatan' => ['required', 'string'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user =  User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'no_telp' => $data['no_telp'],
            'provinsi' => $data['provinsi'],
            'kota' => $data["kota"],
            'kecamatan' => $data['kecamatan'],
            'referal' => $data['referal']
        ]);

        $otp = Str::upper(Str::random(5));

        VerificationCode::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'expire_at' => Carbon::now()->addMinutes(10)
        ]);

        $res = Http::get('http://47.251.18.83/send/'. env('TOKEN_API','TIDAKADA') .'/'.$user->no_telp,[
            'text' => $otp
        ]);

        if($res->body() == 'KEY DATA tidak ada'){
            $user->status = $res->body();
            return $user;
        }

        return $user;
    }
}
