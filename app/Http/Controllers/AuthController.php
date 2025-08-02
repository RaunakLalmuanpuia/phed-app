<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\Otp;
use App\Models\User;
use App\Providers\RouteServiceProvider;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function create(Request $request)
    {
        return inertia('Frontend/Auth/Login',[
        ]);
    }

    public function store(LoginRequest $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function forgotPassword(Request $request)
    {
        return inertia('Frontend/Auth/ForgotPassword',[
        ]);
    }

    private function throwInvalidForgotPasswordError(string $str)
    {
        $error = \Illuminate\Validation\ValidationException::withMessages([
            'userId' => [$str],
        ]);
        throw $error;
    }
    public function sendOtp(Request $request)
    {
        $data=$this->validate($request, [
            'userId' => 'required'
        ]);

        $emailRegex = '/^\\S+@\\S+\\.\\S+$/';
        $mobileRegex = '/^[0-9]{10}+$/';
        $isEmail = preg_match($emailRegex, $data['userId']);
        $isPhone = preg_match($mobileRegex, $data['userId']);

        if ($isEmail) {
            $existEmail=User::query()->where('email', $data['userId'])->exists();
            if (!$existEmail) {
                $this->throwInvalidForgotPasswordError('User not found with emaiil address');
            }
            Otp::query()->create([
                'recipient' => $data['userId'],
                'type' => Otp::EMAIL,
                'otp' =>  env("APP_DEBUG")?1111:rand(0000, 9999),
            ]);

            return response()->json([
                'message' => 'OTP sent to ' . $data['userId'],
                'type'=>Otp::EMAIL
            ]);
        } elseif ($isPhone) {
            $existPhone=User::query()->where('mobile', $data['userId'])->exists();
            if (!$existPhone) {
                $this->throwInvalidForgotPasswordError('User not found with mobile no');
            }
            Otp::query()->create([
                'recipient' => $data['userId'],
                'type' => Otp::MOBILE,
                'otp' => env("APP_DEBUG")?1111:rand(0000, 9999),
            ]);
            return response()->json([
                'message' => 'OTP sent to ' . $data['userId'],
                'type'=>Otp::MOBILE
            ]);
        }else{
            throw ValidationException::withMessages([
                'userId' => ['Invalid email/Mobile']
            ]);
        }



    }
    public function verifyOtp(Request $request)
    {
        $data=$this->validate($request, [
            'userId' => 'required',
            'otp'=>['required'],
        ]);
        $emailRegex = '/^\\S+@\\S+\\.\\S+$/';
        $mobileRegex = '/^[0-9]{10}+$/';

        $isEmail = preg_match($emailRegex, $data['userId']);
        $isPhone = preg_match($mobileRegex, $data['userId']);

        $type = $isEmail ? 'email' : 'mobile';

        $verified=Otp::query()->where('type',$type )
            ->where('recipient',$data['userId'])
            ->where('otp',$data['otp'])->latest()->first();

        return response()->json(['data' => $verified]);

    }
    public function changePassword(Request $request)
    {
        $data=$this->validate($request, [
            'userId' => 'required',
            'password' => 'required|confirmed',
        ]);

        $emailRegex = '/^\\S+@\\S+\\.\\S+$/';
        $mobileRegex = '/^[0-9]{10}+$/';

        $isEmail = preg_match($emailRegex, $data['userId']);
        $isPhone = preg_match($mobileRegex, $data['userId']);

        $user=User::query()->where($isEmail ? 'email' : 'mobile', $data['userId'])->first();

        $user->password = Hash::make($data['password']);
        $user->save();

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Password changed successfully'
        ]);
    }
}
