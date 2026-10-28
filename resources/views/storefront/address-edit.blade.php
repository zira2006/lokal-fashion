
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Alamat | Lokal Fashion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lokal-fashion.css') }}">
    <style>
        .address-page { padding: 35px 0 70px; }
        .address-title { font-family: 'Playfair Display', serif; font-size: 28px; color: var(--dark); margin: 0 0 8px; }
        .address-subtitle { font-size: 13px; color: var(--muted); margin: 0 0 30px; }

        .form-card { background: var(--white); border: 1px solid var(--line); border-radius: 14px; padding: 30px; max-width: 600px; }
        .form-card h2 { font-family: 'Playfair Display', serif; font-size: 19px; color: var(--dark); margin: 0 0 20px; padding-bottom: 12px; border-bottom: 1px solid var(--line); }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: var(--dark); margin-bottom: 7px; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px 15px; border: 1px solid var(--line); border-radius: 10px; font-size: 14px; font-family: inherit; background: #fff; outline: none; }
        .form-group input:focus, .form-group textarea:focus { border-color: var(--brown); }
        .form-error { color: #a8442e; font-size: 12px; margin-top: 5px; }

        .btn { display: inline-block; padding: 12px 26px; border: none; border-radius: 25px; font-size: 14px; font-weight: 600; font-family: inherit; cursor: pointer; text-decoration: none; text-align: center; transition: all 0.2s; }
        .btn-primary { background: var(--dark); color: #fff; }
        .btn-primary:hover { background: #6d5340; }
        .btn-outline { background: transparent; border: 1.5px solid var(--dark); color: var(--dark); }
        .btn-outline:hover { background: var(--dark); color: #fff; }
    </style>
</head>
<body>
    @include('layout.partials.header')

    <main class="container address-page">
        <h1 class="address-title">Edit Alamat</h1>
        <p class="address-subtitle">Perbarui informasi alamat pengiriman</p>

        <div class="form-card">
            <h2>Form Alamat</h2>
            <form method="POST" action="{{ route('profile.addresses.update', $address->id_alamat) }}">
                @csrf
                @method('PUT')
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label for="nama_penerima">Nama Penerima</label>
                        <input type="text" id="nama_penerima" name="nama_penerima" value="{{ old('nama_penerima', $address->nama_penerima) }}" required>
                        @error('nama_penerima') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="no_hp">No. HP</label>
                        <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', $address->no_hp) }}" required>
                        @error('no_hp') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="alamat_lengkap">Alamat Lengkap</label>
                    <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3" required>{{ old('alamat_lengkap', $address->alamat_lengkap) }}</textarea>
                    @error('alamat_lengkap') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label for="kota">Kota</label>
                        <input type="text" id="kota" name="kota" value="{{ old('kota', $address->kota) }}" required>
                        @error('kota') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="kode_pos">Kode Pos</label>
                        <input type="text" id="kode_pos" name="kode_pos" value="{{ old('kode_pos', $address->kode_pos) }}">
                    </div>
                </div>

                @if(!$address->is_default)
                    <label class="checkbox-line">
                        <input type="checkbox" name="is_default" value="1" @checked(old('is_default'))>
                        Jadikan alamat utama
                    </label>
                @endif

                <div style="display:flex; gap:12px;">
                    <a href="{{ route('profile.addresses') }}" class="btn btn-outline">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </main>

    @include('layout.partials.footer')
</body>
</html>
