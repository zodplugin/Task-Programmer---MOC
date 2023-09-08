<?php

namespace App\Http\Controllers;

use App\User;
use App\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home',[
            'users' => User::all()
        ]);
    }

    public function verifyotp($no_telp){
        return view('auth.otp',[
            'no_telp' => $no_telp
        ]);
    }

    public function verified(Request $request){
        $request->validate([
            'no_telp' => 'required|exists:users,no_telp',
            'otp' => 'required'
        ]);

        $user = User::where('no_telp',$request->no_telp)->first();
        if(!$user){
            return redirect()->route('register')->with('error','Nomor Telepon Tidak Ada');
        }

        $verificationCode = VerificationCode::where('user_id', $user->id)->where('otp', $request->otp)->first();
        $now = Carbon::now();
        if (!$verificationCode) {
            return redirect()->back()->with('error', 'OTP Kamu Salah');
        }elseif($verificationCode && $now->isAfter($verificationCode->expire_at)){
            return redirect()->back()->with('error', 'Kode OTP Sudah Expired');
        }

        Auth::login($user);
        return redirect()->route('home')->with('success','Berhasil Login');
    }
}
