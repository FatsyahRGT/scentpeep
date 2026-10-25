<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        // Check user credentials and status
        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            if ($user->status === User::STATUS_PENDING) {
                return back()->withErrors([
                    'email' => 'Pendaftaran akun Anda sedang menunggu verifikasi & persetujuan dari Administrator.',
                ])->onlyInput('email');
            }

            if ($user->status === User::STATUS_REJECTED) {
                return back()->withErrors([
                    'email' => 'Pendaftaran akun Anda ditolak oleh Administrator.',
                ])->onlyInput('email');
            }

            if (! $user->is_active || $user->status === User::STATUS_INACTIVE) {
                return back()->withErrors([
                    'email' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi Administrator.',
                ])->onlyInput('email');
            }

            Auth::login($user, $remember);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang kembali, ' . $user->name . '!');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'location_name' => ['required', 'string', 'max:255', 'unique:locations,name'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar dalam sistem.',
            'location_name.required' => 'Nama toko / lokasi reseller wajib diisi.',
            'location_name.unique' => 'Nama toko / lokasi reseller ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => User::ROLE_RESELLER,
                'status' => User::STATUS_PENDING,
                'is_active' => false,
            ]);

            Location::create([
                'name' => $request->location_name,
                'type' => Location::TYPE_RESELLER,
                'user_id' => $user->id,
            ]);
        });

        return redirect()->route('login')->with('success', 'Pendaftaran reseller berhasil! Akun Anda sedang menunggu persetujuan Administrator.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar (logout).');
    }
}
