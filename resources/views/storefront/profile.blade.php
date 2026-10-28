
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya | Lokal Fashion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lokal-fashion.css') }}">
    <style>
        html { scroll-behavior: smooth; }

        .profile-page { padding: 35px 0 70px; }
        .profile-title { font-family: 'Playfair Display', serif; font-size: 28px; color: var(--dark); margin: 0 0 8px; }
        .profile-subtitle { font-size: 13px; color: var(--muted); margin: 0 0 30px; }

        .profile-layout { display: grid; grid-template-columns: 270px 1fr; gap: 32px; align-items: start; }

        .profile-card { background: var(--white); border: 1px solid var(--line); border-radius: 16px; padding: 30px; margin-bottom: 22px; }
        .profile-card > h2 { font-family: 'Playfair Display', serif; font-size: 20px; color: var(--dark); margin: 0 0 22px; }
        .profile-card-subtitle { font-size: 13px; color: var(--muted); margin: -12px 0 22px; }

        .profile-identity { display: flex; align-items: flex-start; gap: 30px; }
        .profile-fields { flex: 1; min-width: 0; }

        .avatar-block { width: 150px; flex-shrink: 0; text-align: center; }
        .avatar-circle {
            width: 122px;
            height: 122px;
            margin: 0 auto 12px;
            border-radius: 50%;
            background: var(--light-brown);
            border: 1.5px dashed var(--soft-brown);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .avatar-circle.has-photo { border-style: solid; border-color: var(--line); }
        .avatar-circle img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .avatar-initials { font-family: 'Playfair Display', serif; font-size: 38px; font-weight: 600; color: var(--dark); }
        .avatar-form { margin: 0; }
        .avatar-form label { cursor: pointer; }
        .avatar-hint { font-size: 10.5px; color: var(--muted); margin: 7px 0 0; line-height: 1.5; }

        .form-group { margin-bottom: 17px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: var(--dark); margin-bottom: 7px; }
        .form-group input { width: 100%; padding: 12px 15px; border: 1px solid var(--line); border-radius: 10px; font-size: 14px; font-family: inherit; background: #fff; outline: none; transition: border-color 0.2s; }
        .form-group input:focus { border-color: var(--brown); }
        .form-error { color: #a8442e; font-size: 12px; margin-top: 5px; }

        .address-section { margin-top: 10px; padding-top: 24px; border-top: 1px solid var(--line); }
        .address-section h3 { font-family: 'Playfair Display', serif; font-size: 17px; color: var(--dark); margin: 0 0 14px; }

        .primary-address { background: #fdf9f4; border: 1.5px solid var(--line); border-radius: 12px; padding: 17px 19px; }
        .primary-address-top { display: flex; justify-content: space-between; align-items: center; gap: 12px; }
        .primary-address-name { font-weight: 600; font-size: 14px; color: var(--dark); }
        .primary-address-meta { font-size: 12px; color: var(--muted); margin-top: 2px; }
        .primary-address-detail { font-size: 13px; color: var(--text); line-height: 1.6; margin-top: 7px; white-space: pre-line; }
        .primary-address-location { font-size: 12px; color: var(--muted); margin-top: 4px; }
        .primary-address-empty p { margin: 0; font-size: 13px; color: var(--muted); }

        .address-badge { font-size: 10px; background: var(--beige); color: var(--dark); padding: 3px 10px; border-radius: 12px; font-weight: 600; white-space: nowrap; }

        .address-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 14px; }
        .profile-actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 28px; padding-top: 24px; border-top: 1px solid var(--line); }
        .profile-actions .btn { min-width: 165px; }

        .password-card { scroll-margin-top: 100px; }
        .password-card form { max-width: 520px; }

        @media (max-width: 800px) {
            .profile-layout { grid-template-columns: 1fr; }
        }

        @media (max-width: 600px) {
            .profile-card { padding: 24px; }
            .profile-identity { flex-direction: column; align-items: center; }
            .avatar-block { width: 100%; }
            .profile-fields { width: 100%; }
            .profile-actions .btn { flex: 1; min-width: 0; }
        }
    </style>
</head>
<body>
    @include('layout.partials.header')

    @php
        $initials = collect(preg_split('/\s+/', trim($user->nama)) ?: [])
            ->filter()
            ->take(2)
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    @endphp

    <main class="container profile-page">
        <h1 class="profile-title">Profil Saya</h1>
        <p class="profile-subtitle">Kelola informasi akun dan alamat pengirimanmu.</p>

        <div class="profile-layout">
            @include('storefront.partials.account-menu', ['active' => 'profile'])

            <div>
                <div class="profile-card">
                    <h2>Informasi Profil</h2>

                    <div class="profile-identity">
                        <div class="avatar-block">
                            <div class="avatar-circle {{ $user->photo ? 'has-photo' : '' }}">
                                @if($user->photo)
                                    <img src="{{ asset($user->photo) }}" alt="Foto profil {{ $user->nama }}">
                                @else
                                    <span class="avatar-initials">{{ $initials ?: '?' }}</span>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('profile.photo') }}" enctype="multipart/form-data" class="avatar-form">
                                @csrf
                                <input type="file" name="photo" id="photoInput" accept="image/jpeg,image/png,image/webp" hidden
                                       onchange="if (this.files.length) this.form.submit();">
                                <label for="photoInput" class="btn btn-outline btn-sm">Ubah Foto</label>
                            </form>

                            <p class="avatar-hint">JPG/PNG/WEBP<br>maks. 2 MB</p>
                            @error('photo')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <form id="profileForm" method="POST" action="{{ route('profile.update') }}" class="profile-fields">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="name">Nama Lengkap</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->nama) }}" required autocomplete="name">
                                @error('name') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                                @error('email') <div class="form-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-group">
                                <label for="phone">No. HP</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->no_hp) }}" placeholder="08xxxxxxxxxx" autocomplete="tel">
                                @error('phone') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                        </form>
                    </div>

                    <div class="address-section">
                        <h3>Alamat Pengiriman Utama</h3>

                        @if($primaryAddress)
                            <div class="primary-address">
                                <div class="primary-address-top">
                                    <span class="primary-address-name">{{ $primaryAddress->nama_penerima }}</span>
                                    <span class="address-badge">Utama</span>
                                </div>
                                <div class="primary-address-meta">{{ $primaryAddress->no_hp }}</div>
                                <div class="primary-address-detail">{{ trim($primaryAddress->alamat_lengkap) }}</div>
                                <div class="primary-address-location">
                                    {{ $primaryAddress->kota }}@if($primaryAddress->kode_pos) · {{ $primaryAddress->kode_pos }}@endif
                                </div>
                            </div>
                        @else
                            <div class="primary-address primary-address-empty">
                                <p>Belum ada alamat pengiriman. Tambahkan alamat agar bisa checkout.</p>
                            </div>
                        @endif

                        <div class="address-actions">
                            @if($primaryAddress)
                                <a href="{{ route('profile.addresses') }}" class="btn btn-outline btn-sm">Kelola Alamat</a>
                                <a href="{{ route('profile.addresses.edit', $primaryAddress->id_alamat) }}" class="btn btn-outline btn-sm">Ubah Alamat</a>
                            @else
                                <a href="{{ route('profile.addresses') }}#form-alamat" class="btn btn-outline btn-sm">Lengkapi Alamat</a>
                            @endif
                        </div>
                    </div>

                    <div class="profile-actions">
                        <button type="submit" form="profileForm" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="#ganti-password" class="btn btn-outline">Ganti Password</a>
                    </div>
                </div>

                <div class="profile-card password-card" id="ganti-password">
                    <h2>Ganti Password</h2>
                    <p class="profile-card-subtitle">Gunakan password yang kuat dan mudah kamu ingat.</p>

                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="current_password">Password Saat Ini</label>
                            <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
                            @error('current_password') <div class="form-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label for="new_password">Password Baru</label>
                            <input type="password" id="new_password" name="new_password" required minlength="6" autocomplete="new-password">
                            @error('new_password') <div class="form-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label for="new_password_confirmation">Konfirmasi Password Baru</label>
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" required autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn btn-primary">Perbarui Password</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    @include('layout.partials.footer')
</body>
</html>
