<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistrationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    public function login(Request $request): View|RedirectResponse
    {
        if ($request->method() === 'POST') {
            $validatedData = $request->validate([
                'email' => 'required|email|exists:users,email',
                'password' => 'required',
            ]);

            $rememberMe = $request->has('remember_me');

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $rememberMe)) {
                $user = Auth::user();

                if ($user !== null && ! $user->hasVerifiedEmail()) {
                    Auth::logout();
                    $user->sendEmailVerificationNotification();

                    return redirect()
                        ->route('verification.notice', ['email' => $user->email])
                        ->with('error', __('Please verify your email before logging in.'));
                }

                return redirect()->route('dashboard')->with('success', __('signin_success'));
            }

            return redirect()->route('login')->with('error', __('signin_error'));
        }

        return view('frontend.auth.login');
    }

    public function register(): View
    {
        return view('frontend.auth.register');
    }

    public function storeRegistration(StoreRegistrationRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => $request->validated('password'),
            'role' => User::ROLE_VIEWER,
        ]);

        $user->sendEmailVerificationNotification();

        return redirect()
            ->route('verification.notice', ['email' => $user->email])
            ->with('success', __('Registration successful. Please verify your email before logging in.'));
    }

    public function verificationNotice(Request $request): View
    {
        return view('frontend.auth.verify-notice', [
            'email' => (string) $request->query('email', session('registered_email', '')),
        ]);
    }

    public function verifyEmail(Request $request, string $id, string $hash): RedirectResponse
    {
        $user = User::query()->findOrFail($id);

        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            abort(403);
        }

        if (! $user->hasVerifiedEmail()) {
            $user->forceFill(['email_verified_at' => now()])->save();
            event(new Verified($user));
        }

        return redirect()
            ->route('login')
            ->with('success', __('Email verified successfully. You can now log in.'));
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();
        return redirect()->route('login')->with('success', __('signout_success'));
    }

    public function setLanguage($locale): RedirectResponse
    {
        if (!in_array($locale, ['en', 'de'])) {
            abort(400);
        }

        Session::put('locale', $locale);
        App::setLocale($locale);

        return redirect()->back();
    }


    public function changePassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        $response = array();
        $user = Auth::user();
        $old_password = $request->old_password;
        $new_password = $request->new_password;
        if (Hash::check($old_password, $user->getAuthPassword())) {
            $user->password = Hash::make($new_password);
            if ($user->save()) {
                $response['flag'] = true;
                $response['message'] = __('Password changed successfully');
            } else {
                $response['flag'] = false;
                $response['error'] = __('something_went_wrong');
            }
        } else {
            $response['flag'] = false;
            $response['message'] = __('Invalid old password');
        }

        return response()->json($response, 200);
    }
}
