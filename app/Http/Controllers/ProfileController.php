<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $user->load([
            'addresses' => fn ($query) => $query
                ->orderByDesc('is_default')
                ->orderBy('id_alamat'),
        ]);

        $primaryAddress = $user->addresses->firstWhere('is_default', true)
            ?? $user->addresses->first();

        return view('storefront.profile', compact('user', 'primaryAddress'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('user', 'email')->ignore($user->id_user),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update([
            'nama' => $validated['name'],
            'email' => $validated['email'],
            'no_hp' => $validated['phone'] ?? null,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'photo.required' => 'Pilih foto terlebih dahulu.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Foto harus berformat JPG, PNG, atau WEBP.',
            'photo.max' => 'Ukuran foto maksimal 2 MB.',
        ]);

        $user = $request->user();
        $file = $request->file('photo');
        $directory = public_path('uploads/profile');

        File::ensureDirectoryExists($directory);

        $filename = $file->hashName();
        $file->move($directory, $filename);

        if ($user->photo && str_starts_with($user->photo, 'uploads/profile/')) {
            $oldPath = public_path($user->photo);

            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }
        }

        $user->update(['photo' => 'uploads/profile/'.$filename]);

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $request->user()->update([
            'password_hash' => Hash::make($validated['new_password']),
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    public function addresses(): View
    {
        $addresses = Address::where('id_user', Auth::id())
            ->orderByDesc('is_default')
            ->latest('id_alamat')
            ->get();

        return view('storefront.addresses', compact('addresses'));
    }

    public function storeAddress(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_penerima' => ['required', 'string', 'max:100'],
            'no_hp' => ['required', 'string', 'max:20'],
            'alamat_lengkap' => ['required', 'string'],
            'kota' => ['required', 'string', 'max:100'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
        ]);

        if (Address::where('id_user', Auth::id())->exists()) {
            return back()->with('error', 'Hapus alamat lama terlebih dahulu sebelum menambahkan alamat baru.');
        }

        $validated['id_user'] = Auth::id();

        $isFirstAddress = Address::where('id_user', Auth::id())->count() === 0;
        $makeDefault = $isFirstAddress || $request->boolean('is_default');

        if ($makeDefault) {
            Address::where('id_user', Auth::id())->update(['is_default' => false]);
        }

        $validated['is_default'] = $makeDefault;

        Address::create($validated);

        if ($request->input('redirect') === 'checkout') {
            return redirect()->route('checkout')
                ->with('success', 'Alamat berhasil ditambahkan. Silakan lanjutkan pesanan.');
        }

        return redirect()->route('profile.addresses')
            ->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function editAddress(int $id): View
    {
        $address = Address::where('id_alamat', $id)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        return view('storefront.address-edit', compact('address'));
    }

    public function updateAddress(Request $request, int $id): RedirectResponse
    {
        $address = Address::where('id_alamat', $id)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        $validated = $request->validate([
            'nama_penerima' => ['required', 'string', 'max:100'],
            'no_hp' => ['required', 'string', 'max:20'],
            'alamat_lengkap' => ['required', 'string'],
            'kota' => ['required', 'string', 'max:100'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
        ]);

        if ($request->boolean('is_default')) {
            Address::where('id_user', Auth::id())->update(['is_default' => false]);
            $validated['is_default'] = true;
        }

        $address->update($validated);

        return redirect()->route('profile.addresses')
            ->with('success', 'Alamat berhasil diperbarui.');
    }

    public function setDefaultAddress(int $id): RedirectResponse
    {
        $address = Address::where('id_alamat', $id)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        Address::where('id_user', Auth::id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('success', 'Alamat utama berhasil diubah.');
    }

    public function deleteAddress(int $id): RedirectResponse
    {
        $address = Address::where('id_alamat', $id)
            ->where('id_user', Auth::id())
            ->firstOrFail();

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            Address::where('id_user', Auth::id())
                ->orderBy('id_alamat')
                ->first()
                ?->update(['is_default' => true]);
        }

        return redirect()->route('profile.addresses')
            ->with('success', 'Alamat berhasil dihapus.');
    }
}
