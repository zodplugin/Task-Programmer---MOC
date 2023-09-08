<?php

namespace App\Http\Controllers;

use App\User;
use App\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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

    public function verifiedotp(){
        return view('auth.verifyotp');
    }

    public function verifiedotpcheck(Request $request){
        $request->validate([
            'no_telp' => 'required|string|exists:users,no_telp'
        ]);

        $user = User::where('no_telp',$request->no_telp)->first();
        if(!$user){
            return redirect()->route('register')->with('error','Nomor Telepon Tidak Ada');
        }

        return redirect()->route('verifyotp',$request->no_telp)->with('success','Masukkan Kode OTP');
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

    public function otp(){
        return view('auth.generateotp');
    }

    public function generateotp(Request $request){
        $user = User::where('no_telp',$request->no_telp)->first();
        if(!$user){
            return redirect()->back()->with('error','Nomor Telepon Tidak Ada');
        }

        $verification = VerificationCode::where('user_id',$user->id)->first();
        if(!$verification){
            return redirect()->back()->with('error','Nomor Telepon Tidak Ada');
        }

        $now = Carbon::now();
        if($now->isBefore($verification->expire_at)){
            return redirect()->route('verifyotp',$user->no_telp)->with('error','Kode OTP Tidak Expired Silahkan Masukkan Kode OTP Sebelumnya');
        }


        $otp = Str::upper(Str::random(5));
        $verification->update([
            'otp' => $otp,
            'expire_at' => Carbon::now()->addMinutes(10)
        ]);

        Http::get('http://47.251.18.83/send/XjhGkWLRp5sqivC0yaT6/'.$user->no_telp,[
            'text' => $otp
        ]);

        return redirect(route('verifyotp',$user->no_telp))->with('success','Generate OTP Berhasil');
    }


}
