<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Send OTP to email for registration verification.
     */
    public function sendRegisterOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store OTP in cache for 10 minutes with registration data
        Cache::put('register_otp_' . $request->email, [
            'otp' => $otp,
            'email' => $request->email,
            'expires_at' => now()->addMinutes(10),
        ], now()->addMinutes(10));

        // Send OTP email - if fails, email is not valid/registered
        try {
            Mail::raw(
                "Kode OTP Registrasi Ringkesin\n\n" .
                "Kode OTP Anda: {$otp}\n\n" .
                "Kode ini berlaku selama 10 menit.\n" .
                "Jangan bagikan kode ini kepada siapa pun.\n\n" .
                "Jika Anda tidak mendaftar di Ringkesin, abaikan email ini.\n\n" .
                "Salam,\n" .
                "Tim Ringkesin",
                function ($message) use ($request) {
                    $message->to($request->email)
                        ->subject('Kode OTP Registrasi - Ringkesin');
                }
            );

            return response()->json([
                'success' => true,
                'message' => 'Kode OTP telah dikirim ke email Anda. Periksa inbox atau folder spam.',
                'expires_in' => 600, // 10 minutes in seconds
            ]);
        } catch (\Exception $e) {
            // Clean up OTP cache if email sending fails
            Cache::forget('register_otp_' . $request->email);
            
            Log::error('Failed to send OTP email: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Email tidak terdaftar atau tidak valid. Pastikan Anda menggunakan email aktif yang benar.',
            ], 422);
        }
    }

    /**
     * Handle registration request with OTP verification.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'otp_code' => ['required', 'string', 'size:6'],
        ]);

        // Verify OTP
        $cachedData = Cache::get('register_otp_' . $validated['email']);
        
        if (!$cachedData) {
            return back()->withErrors([
                'otp_code' => 'Kode OTP tidak valid atau sudah kadaluarsa. Silakan kirim ulang kode OTP.',
            ])->withInput();
        }

        if ($cachedData['otp'] !== $validated['otp_code']) {
            return back()->withErrors([
                'otp_code' => 'Kode OTP salah. Periksa kembali email Anda.',
            ])->withInput();
        }

        // OTP verified, create user with verified email
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(), // Email already verified via OTP
        ]);

        // Clean up OTP cache
        Cache::forget('register_otp_' . $validated['email']);

        // Auto-login
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Akun berhasil dibuat! Selamat datang di Ringkesin.');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
