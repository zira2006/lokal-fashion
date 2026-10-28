
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Toko | Pusat Penjual - Lokal Fashion</title>
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
        .seller-header h1 { font-family: 'Playfair Display', serif; font-size: 26px; color: var(--dark); margin: 0 0 8px; }
        .seller-header p { font-size: 13px; color: var(--muted); margin: 0 0 28px; }

        .greeting { font-size: 13px; color: var(--muted); margin-bottom: 25px; }
        .greeting strong { color: var(--dark); }

        .profile-card {
            background: var(--white);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 35px;
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 40px;
        }

        .profile-avatar {
            text-align: center;
        }

        .avatar-placeholder {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: var(--beige);
            border: 3px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            color: var(--brown);
            margin: 0 auto 18px;
            overflow: hidden;
        }

        .avatar-placeholder img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .store-name {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 6px;
        }

        .store-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-verified { background: #d9edda; color: #3d6b3e; }
        .badge-pending { background: #fdf0d5; color: #8a6d1a; }

        .profile-info h2 {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            color: var(--dark);
            margin: 0 0 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--line);
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .info-item {
            margin-bottom: 16px;
        }

        .info-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--muted);
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 14px;
            color: var(--dark);
            font-weight: 500;
        }

        .profile-actions {
            display: flex;
            gap: 12px;
            margin-top: 25px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            border: none;
            border-radius: 25px;
            font-size: 13px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s;
        }

        .btn-primary { background: var(--dark); color: #fff; }
        .btn-primary:hover { background: #6d5340; }
        .btn-outline { background: transparent; border: 1.5px solid var(--dark); color: var(--dark); }
        .btn-outline:hover { background: var(--dark); color: #fff; }
        .btn-sm { padding: 8px 16px; font-size: 12px; }

        .avatar-form { margin-top: 2px; }
        .avatar-hint { font-size: 11px; color: var(--muted); margin: 8px 0 0; line-height: 1.5; }
        .form-error { color: #a8442e; font-size: 12px; margin-top: 6px; }

        .flash { padding: 13px 18px; border-radius: 10px; font-size: 13px; margin-bottom: 20px; }
        .flash-success { background: #e5f2e5; border: 1px solid #b9d8b9; color: #3d6b3e; }
        .flash-error { background: #f8e7e3; border: 1px solid #e7c0b7; color: #8e3624; }

        .store-description {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid var(--line);
        }

        .store-description h3 {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--muted);
            margin: 0 0 10px;
        }

        .store-description p {
            font-size: 14px;
            color: var(--text);
            line-height: 1.7;
        }

        @media (max-width: 800px) {
            .seller-page { grid-template-columns: 1fr; }
            .seller-sidebar { padding: 15px 0; }
            .seller-nav { flex-direction: row; overflow-x: auto; }
            .seller-nav a { white-space: nowrap; border-left: none; border-bottom: 3px solid transparent; }
            .seller-nav a:hover, .seller-nav a.active { border-bottom-color: var(--brown); }
            .seller-main { padding: 25px 18px; }
            .profile-card { grid-template-columns: 1fr; gap: 25px; padding: 25px; }
            .info-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="seller-page">
                @include('layout.partials.dashboard-sidebar')

        <main class="seller-main">
            @if(session('success'))
                <div class="flash flash-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="flash flash-error">{{ session('error') }}</div>
            @endif

            <p class="greeting">Halo, <strong>{{ Auth::user()->nama }}</strong></p>

            <div class="seller-header">
                <h1>Profil Toko</h1>
                <p>Kelola informasi tokomu di sini</p>
            </div>

            <div class="profile-card">
                <div class="profile-avatar">
                    <div class="avatar-placeholder">
                        @if($user->photo)
                            <img src="{{ asset($user->photo) }}" alt="Foto profil {{ $user->nama }}">
                        @elseif($store->logo)
                            <img src="{{ $store->logo }}" alt="{{ $store->nama_toko }}">
                        @else
                            {{ strtoupper(substr($store->nama_toko, 0, 1)) }}
                        @endif
                    </div>

                    <form method="POST" action="{{ route('seller.profile.photo') }}" enctype="multipart/form-data" class="avatar-form">
                        @csrf
                        <input type="file" name="photo" id="sellerPhotoInput" accept="image/jpeg,image/png,image/webp" hidden
                               onchange="if (this.files.length) this.form.submit();">
                        <label for="sellerPhotoInput" class="btn btn-outline btn-sm">{{ $user->photo ? 'Ubah Foto' : 'Tambah Foto' }}</label>
                    </form>

                    <p class="avatar-hint">JPG/PNG/WEBP<br>maks. 2 MB</p>
                    @error('photo')
                        <div class="form-error">{{ $message }}</div>
                    @enderror

                    <div class="store-name">{{ $store->nama_toko }}</div>
                    <span class="store-badge {{ $store->status_verifikasi === 'terverifikasi' ? 'badge-verified' : 'badge-pending' }}">
                        @if($store->status_verifikasi === 'terverifikasi')
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg>
                        @endif
                        {{ ucfirst($store->status_verifikasi) }}
                    </span>
                </div>

                <div class="profile-info">
                    <h2>Informasi Toko</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Nama Toko</div>
                            <div class="info-value">{{ $store->nama_toko }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value">{{ Auth::user()->email }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">No. HP</div>
                            <div class="info-value">{{ Auth::user()->no_hp ?: '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Kota Asal</div>
                            <div class="info-value">{{ $store->kota_asal ?: '-' }}</div>
                        </div>
                    </div>

                    <div class="store-description">
                        <h3>Deskripsi Toko</h3>
                        <p>{{ $store->deskripsi ?: 'Belum ada deskripsi toko.' }}</p>
                    </div>

                    <div class="profile-actions">
                        <a href="{{ route('seller.profile.edit') }}" class="btn btn-primary">Simpan Perubahan</a>
                        <a href="{{ route('seller.profile.edit') }}" class="btn btn-outline">Ganti Password</a>
                        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn btn-outline">Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
