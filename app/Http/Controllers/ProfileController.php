<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function index()
    {
        $user = Auth::user();
        $totalNotes = \App\Models\Note::where('user_id', $user->id)->count();
        $totalCategories = \App\Models\Category::where('user_id', $user->id)->count();
        $daysJoined = now()->diffInDays($user->created_at);

        return view('profile.index', compact('user', 'totalNotes', 'totalCategories', 'daysJoined'));
    }

    /**
     * Update the user's profile (without email).
     */
    public function update(Request $request)
    {
        $user = \App\Models\User::findOrFail(Auth::id());

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:500'],
        ]);

        $user->name = $validated['name'];
        $user->bio = $validated['bio'] ?? null;
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Request email change - Send OTP to old email and verification link to new email.
     */
    public function requestEmailChange(Request $request)
    {
        $request->validate([
            'new_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        ]);

        $user = Auth::user();
        $newEmail = $request->new_email;

        // Generate OTP code (6 digits)
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Generate verification token
        $verificationToken = Str::random(64);

        // Store in cache for 15 minutes
        $cacheKey = 'email_change_' . $user->id;
        Cache::put($cacheKey, [
            'user_id' => $user->id,
            'old_email' => $user->email,
            'new_email' => $newEmail,
            'otp_code' => $otpCode,
            'created_at' => now(),
        ], now()->addMinutes(15));

        try {
            // Gunakan Resend API (HTTP) untuk mengirim email
            $resend = \Resend::client(config('services.resend.key'));
            
            $resend->emails->send([
                'from' => config('mail.from.address'),
                'to' => [$newEmail],
                'subject' => 'Kode OTP Verifikasi Email Baru - Ringkesin',
                'text' => "Halo {$user->name},\n\n" .
                         "Anda meminta untuk mengubah email akun Ringkesin ke {$newEmail}.\n\n" .
                         "Kode OTP verifikasi Anda adalah: {$otpCode}\n\n" .
                         "Kode ini berlaku selama 15 menit.\n\n" .
                         "Jika Anda tidak meminta perubahan ini, abaikan email ini.\n\n" .
                         "Salam,\nTim Ringkesin",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kode OTP telah dikirim ke email BARU Anda (' . $newEmail . ').',
            ]);

        } catch (\Exception $e) {
            \Log::error('Resend API Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Gagal mengirim email: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify OTP and complete email change.
     */
    public function verifyEmailChange(Request $request)
    {
        $request->validate([
            'new_email' => ['required', 'email'],
            'otp_code' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();
        $cacheKey = 'email_change_' . $user->id;
        $changeData = Cache::get($cacheKey);

        if (!$changeData) {
            return response()->json([
                'success' => false,
                'error' => 'Sesi verifikasi telah kadaluarsa. Silakan minta kode baru.',
            ], 400);
        }

        // Verify OTP
        if ($changeData['otp_code'] !== $request->otp_code) {
            return response()->json([
                'success' => false,
                'error' => 'Kode OTP tidak valid.',
            ], 400);
        }

        // Verify email matches
        if ($changeData['new_email'] !== $request->new_email) {
            return response()->json([
                'success' => false,
                'error' => 'Email tidak sesuai dengan permintaan.',
            ], 400);
        }

        // Check if new email is still available
        $emailExists = \App\Models\User::where('email', $request->new_email)
            ->where('id', '!=', $user->id)
            ->exists();

        if ($emailExists) {
            return response()->json([
                'success' => false,
                'error' => 'Email sudah digunakan oleh akun lain.',
            ], 400);
        }

        // Update user email immediately
        $user = \App\Models\User::findOrFail($user->id);
        $oldEmail = $user->email;
        $user->email = $request->new_email;
        $user->email_verified_at = now();
        $user->save();

        // Clear cache
        Cache::forget($cacheKey);

        return response()->json([
            'success' => true,
            'message' => 'Email berhasil diubah!',
        ]);
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = \App\Models\User::findOrFail(Auth::id());

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
        ]);

        // Update password
        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return back()->with('success', 'Password berhasil diubah!');
    }
}
