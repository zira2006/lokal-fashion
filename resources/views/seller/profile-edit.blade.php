
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Toko | Pusat Penjual - Lokal Fashion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lokal-fashion.css') }}">
    <style>
        .seller-page { display: grid; grid-template-columns: 240px 1fr; min-height: 100vh; }
        .seller-sidebar { background: var(--dark); padding: 30px 0; }
        .seller-sidebar .brand { display: block; padding: 0 26px 28px; color: #f5ebe0; font-family: 'Playfair Display', serif; font-size: 19px; font-weight: 700; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,.1); margin-bottom: 20px; }
        .seller-nav { display: flex; flex-direction: column; }
        .seller-nav a { padding: 13px 26px; color: #d4c3b2; text-decoration: none; font-size: 14px; transition: all 0.2s; border-left: 3px solid transparent; }
        .seller-nav a:hover, .seller-nav a.active { color: #fff; background: rgba(255,255,255,.07); border-left-color: var(--brown); }
        .seller-main { padding: 35px 40px; }
        .seller-header h1 { font-family: 'Playfair Display', serif; font-size: 26px; color: var(--dark); margin: 0 0 28px; }

        .greeting { font-size: 13px; color: var(--muted); margin-bottom: 25px; }
        .greeting strong { color: var(--dark); }

        .form-card { background: var(--white); border: 1px solid var(--line); border-radius: 14px; padding: 30px; margin-bottom: 22px; }
        .form-card h2 { font-family: 'Playfair Display', serif; font-size: 18px; color: var(--dark); margin: 0 0 20px; padding-bottom: 12px; border-bottom: 1px solid var(--line); }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: var(--dark); margin-bottom: 7px; }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--line);
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            background: #fff;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-group input:focus, .form-group textarea:focus { border-color: var(--brown); }
        .form-error { color: #a8442e; font-size: 12px; margin-top: 5px; }
        .form-hint { font-size: 11.5px; color: var(--muted); margin: 7px 0 0; line-height: 1.5; }

        .btn { display: inline-block; padding: 13px 28px; border: none; border-radius: 25px; font-size: 14px; font-weight: 600; font-family: inherit; cursor: pointer; text-decoration: none; text-align: center; transition: all 0.2s; }
        .btn-primary { background: var(--dark); color: #fff; }
        .btn-primary:hover { background: #6d5340; }
        .btn-outline { background: transparent; border: 1.5px solid var(--dark); color: var(--dark); }
        .btn-outline:hover { background: var(--dark); color: #fff; }

        .avatar-section { text-align: center; margin-bottom: 25px; }
        .avatar-preview { width: 120px; height: 120px; border-radius: 50%; background: var(--beige); border: 3px solid var(--line); display: flex; align-items: center; justify-content: center; font-family: 'Playfair Display', serif; font-size: 36px; color: var(--brown); margin: 0 auto 15px; overflow: hidden; }
        .avatar-preview img { width: 100%; height: 100%; object-fit: cover; }

        @media (max-width: 800px) {
            .seller-page { grid-template-columns: 1fr; }
            .seller-sidebar { padding: 15px 0; }
            .seller-nav { flex-direction: row; overflow-x: auto; }
            .seller-nav a { white-space: nowrap; border-left: none; border-bottom: 3px solid transparent; }
            .seller-nav a:hover, .seller-nav a.active { border-bottom-color: var(--brown); }
            .seller-main { padding: 25px 18px; }
            .form-card { padding: 22px 18px; }
        }
    </style>
</head>
<body>
    <div class="seller-page">
                @include('layout.partials.dashboard-sidebar')

        <main class="seller-main">
            <p class="greeting">Halo, <strong>{{ Auth::user()->nama }}</strong></p>

            <div class="seller-header">
                <h1>Edit Profil Toko</h1>
            </div>

            <form method="POST" action="{{ route('seller.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-card">
                    <h2>Informasi Toko</h2>

                    <div class="avatar-section">
                        <div class="avatar-preview">
                            @if($user->photo)
                                <img src="{{ asset($user->photo) }}" alt="Foto profil {{ $user->nama }}">
                            @elseif($store->logo)
                                <img src="{{ $store->logo }}" alt="{{ $store->nama_toko }}">
                            @else
                                {{ strtoupper(substr($store->nama_toko, 0, 1)) }}
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="photo">Foto Profil</label>
                        <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp">
                        <p class="form-hint">Pilih dari galeri. Kosongkan jika tidak ingin mengganti. JPG/PNG/WEBP, maks. 2 MB.</p>
                        @error('photo') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="nama_toko">Nama Toko</label>
                        <input type="text" id="nama_toko" name="nama_toko" value="{{ old('nama_toko', $store->nama_toko) }}" required>
                        @error('nama_toko') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi Toko</label>
                        <textarea id="deskripsi" name="deskripsi" rows="4">{{ old('deskripsi', $store->deskripsi) }}</textarea>
                        @error('deskripsi') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="kota_asal">Kota Asal</label>
                        <input type="text" id="kota_asal" name="kota_asal" value="{{ old('kota_asal', $store->kota_asal) }}">
                        @error('kota_asal') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-card">
                    <h2>Informasi Akun</h2>
                    <div class="form-group">
                        <label for="phone">No. HP</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->no_hp) }}">
                        @error('phone') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-card">
                    <h2>Ganti Password</h2>
                    <div class="form-group">
                        <label for="current_password">Password Saat Ini</label>
                        <input type="password" id="current_password" name="current_password">
                        @error('current_password') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="new_password">Password Baru</label>
                        <input type="password" id="new_password" name="new_password">
                        @error('new_password') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="new_password_confirmation">Konfirmasi Password Baru</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation">
                    </div>
                </div>

                <div style="display: flex; gap: 12px;">
                    <a href="{{ route('seller.profile') }}" class="btn btn-outline">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
