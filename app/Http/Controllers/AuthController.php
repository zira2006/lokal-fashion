<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    private const ROLE_LABELS = [
        'pembeli' => 'Pembeli',
        'penjual' => 'Penjual',
        'admin' => 'Admin',
    ];

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->intended($this->redirectTo());
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'role' => ['required', 'in:pembeli,penjual,admin'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password_hash)) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->withInput($request->only('email', 'role'));
        }

        $buyerBypass = $credentials['role'] === 'pembeli' && $user->role === 'penjual';
        $sellerViaAdmin = $credentials['role'] === 'admin' && $user->role === 'penjual';

        if ($user->role !== $credentials['role'] && ! $buyerBypass && ! $sellerViaAdmin) {
            $actualRole = self::ROLE_LABELS[$user->role] ?? ucfirst($user->role);

            return back()->withErrors([
                'role' => "Akun ini terdaftar sebagai {$actualRole}. Pilih tipe akun yang sesuai untuk melanjutkan.",
            ])->withInput($request->only('email', 'role'));
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Penjual dan admin selalu masuk langsung ke dashboard sesuai role.
        if (in_array($user->role, ['penjual', 'admin'], true)) {
            return redirect()->route('dashboard');
        }

        return redirect()->intended(route('home'));
    }

    public function showSellerLogin(): RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->intended($this->redirectTo());
        }

        return redirect()->route('login', ['role' => 'admin']);
    }

    public function showAdminLogin(): RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->intended($this->redirectTo());
        }

        return redirect()->route('login', ['role' => 'admin']);
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:user,email'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'min:6', 'confirmed'],
            'role' => ['required', 'in:pembeli,penjual'],
        ]);

        $user = User::create([
            'nama' => $validated['name'],
            'email' => $validated['email'],
            'no_hp' => $validated['phone'],
            'password_hash' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        if ($validated['role'] === 'penjual') {
            $user->store()->create([
                'nama_toko' => 'Toko '.$validated['name'],
                'status_verifikasi' => 'menunggu',
                'kota_asal' => 'Banda Aceh',
            ]);

            Registration::create([
                'id_user' => $user->id_user,
                'tgl_registrasi' => now(),
                'status_registrasi' => 'menunggu',
            ]);
        }

        Auth::login($user);

        if ($user->role === 'penjual') {
            return redirect()->route('dashboard');
        }

        return redirect()->intended(route('home'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    protected function redirectTo(): string
    {
        $user = Auth::user();

        if (! $user) {
            return route('home');
        }

        return match ($user->role) {
            'admin', 'penjual' => route('dashboard'),
            default => route('home'),
        };
    }
}
