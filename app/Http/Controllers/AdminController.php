<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Registration;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): RedirectResponse
    {
        return redirect()->route('dashboard');
    }

    public function users(): View
    {
        $users = User::with('store')
            ->whereIn('role', ['pembeli', 'penjual'])
            ->latest()
            ->paginate(15);

        return view('admin.users', compact('users'));
    }

    public function sellers(): View
    {
        $sellers = Store::with(['user', 'products'])
            ->whereHas('user', fn ($query) => $query->where('role', 'penjual'))
            ->orderBy('status_verifikasi')
            ->latest()
            ->paginate(15);

        return view('admin.sellers', compact('sellers'));
    }

    public function verifyStore(int $id): RedirectResponse
    {
        $store = Store::findOrFail($id);
        $store->update([
            'status_verifikasi' => 'terverifikasi',
            'id_admin' => $this->currentAdminId() ?? $store->id_admin,
        ]);

        $this->syncRegistrationStatus($store, 'terverifikasi');

        return back()->with('success', "Toko {$store->nama_toko} berhasil diverifikasi.");
    }

    public function rejectStore(int $id): RedirectResponse
    {
        $store = Store::findOrFail($id);
        $store->update([
            'status_verifikasi' => 'ditolak',
            'id_admin' => $this->currentAdminId() ?? $store->id_admin,
        ]);

        $this->syncRegistrationStatus($store, 'ditolak');

        return back()->with('success', "Toko {$store->nama_toko} ditolak.");
    }

    private function currentAdminId(): ?int
    {
        $user = Auth::user();

        if (! $user) {
            return null;
        }

        $admin = $user->admin()->firstOrCreate([], [
            'nama' => $user->nama,
            'email' => $user->email,
            'password_hash' => $user->password_hash,
        ]);

        return $admin->id_admin;
    }

    private function syncRegistrationStatus(Store $store, string $status): void
    {
        if (! $store->id_user) {
            return;
        }

        $registration = Registration::firstOrCreate(
            ['id_user' => $store->id_user],
            ['tgl_registrasi' => now()]
        );

        $registration->update(['status_registrasi' => $status]);
    }

    public function orders(): View
    {
        $orders = Order::with(['user', 'address', 'items.variant.product'])
            ->placed()
            ->latest()
            ->paginate(15);

        return view('admin.orders', compact('orders'));
    }
}
