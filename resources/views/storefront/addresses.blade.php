
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alamat Pengiriman | Lokal Fashion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lokal-fashion.css') }}">
    <style>
        .address-page { padding: 35px 0 70px; }
        .address-title { font-family: 'Playfair Display', serif; font-size: 28px; color: var(--dark); margin: 0 0 8px; }
        .address-subtitle { font-size: 13px; color: var(--muted); margin: 0 0 30px; }

        .address-layout { display: grid; grid-template-columns: 280px 1fr; gap: 35px; align-items: start; }

        .address-sidebar { background: var(--white); border: 1px solid var(--line); border-radius: 14px; padding: 25px; }
        .address-sidebar h3 { font-family: 'Playfair Display', serif; font-size: 17px; color: var(--dark); margin: 0 0 18px; padding-bottom: 12px; border-bottom: 1px solid var(--line); }
        .address-menu { display: flex; flex-direction: column; }
        .address-menu a { padding: 12px 16px; border-radius: 10px; font-size: 14px; color: var(--text); text-decoration: none; transition: all 0.2s; }
        .address-menu a:hover, .address-menu a.active { background: var(--cream); color: var(--dark); font-weight: 600; }

        .address-card { background: var(--white); border: 1px solid var(--line); border-radius: 14px; padding: 22px 26px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; }
        .address-card-main { flex: 1; min-width: 0; }
        .address-card-header { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 6px; }
        .address-card-name { font-weight: 600; font-size: 15px; color: var(--dark); }
        .address-card-phone { font-size: 12px; color: var(--muted); margin-top: 3px; }
        .address-card-detail { font-size: 13px; color: var(--text); line-height: 1.6; margin-top: 6px; white-space: pre-line; }
        .address-card-location { font-size: 12px; color: var(--muted); margin-top: 4px; }
        .address-card-actions { display: flex; gap: 8px; flex-shrink: 0; flex-wrap: wrap; justify-content: flex-end; }
        .address-badge { font-size: 10px; background: var(--beige); color: var(--dark); padding: 3px 10px; border-radius: 12px; font-weight: 600; white-space: nowrap; }

        .btn { display: inline-block; padding: 12px 26px; border: none; border-radius: 25px; font-size: 14px; font-weight: 600; font-family: inherit; cursor: pointer; text-decoration: none; text-align: center; transition: all 0.2s; }
        .btn-primary { background: var(--dark); color: #fff; }
        .btn-primary:hover { background: #6d5340; }
        .btn-outline { background: transparent; border: 1.5px solid var(--dark); color: var(--dark); }
        .btn-outline:hover { background: var(--dark); color: #fff; }
        .btn-danger { background: transparent; border: 1.5px solid #a8442e; color: #a8442e; }
        .btn-danger:hover { background: #a8442e; color: #fff; }
        .btn-sm { padding: 8px 16px; font-size: 12px; }

        .address-empty { text-align: center; padding: 50px 20px; background: var(--white); border: 1px dashed var(--line); border-radius: 14px; color: var(--muted); }

        .form-card { background: var(--white); border: 1px solid var(--line); border-radius: 14px; padding: 30px; margin-bottom: 22px; }
        .form-card h2 { font-family: 'Playfair Display', serif; font-size: 19px; color: var(--dark); margin: 0 0 20px; padding-bottom: 12px; border-bottom: 1px solid var(--line); }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: var(--dark); margin-bottom: 7px; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px 15px; border: 1px solid var(--line); border-radius: 10px; font-size: 14px; font-family: inherit; background: #fff; outline: none; transition: border-color 0.2s; }
        .form-group input:focus, .form-group textarea:focus { border-color: var(--brown); }
        .form-error { color: #a8442e; font-size: 12px; margin-top: 5px; }

        @media (max-width: 800px) {
            .address-layout { grid-template-columns: 1fr; }
        }

        @media (max-width: 600px) {
            .address-card { flex-direction: column; }
            .address-card-actions { width: 100%; justify-content: flex-start; }
        }
    </style>
</head>
<body>
    @include('layout.partials.header')

    <main class="container address-page">
        <h1 class="address-title">Alamat Pengiriman</h1>
        <p class="address-subtitle">Kelola alamat pengiriman untuk memudahkan checkout</p>

        <div class="address-layout">
            @include('storefront.partials.account-menu', ['active' => 'addresses'])

            <div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:15px;">
                    <h2 style="font-family:'Playfair Display',serif; font-size:20px; color:var(--dark); margin:0;">Daftar Alamat</h2>
                </div>

                @forelse($addresses as $addr)
                    <div class="address-card">
                        <div class="address-card-main">
                            <div class="address-card-header">
                                <span class="address-card-name">{{ $addr->nama_penerima }}</span>
                                @if($addr->is_default)
                                    <span class="address-badge">Utama</span>
                                @endif
                            </div>
                            <div class="address-card-phone">{{ $addr->no_hp }}</div>
                            <div class="address-card-detail">{{ trim($addr->alamat_lengkap) }}</div>
                            <div class="address-card-location">
                                {{ $addr->kota }}@if($addr->kode_pos) · {{ $addr->kode_pos }}@endif
                            </div>
                        </div>
                        <div class="address-card-actions">
                            @if(!$addr->is_default)
                                <form method="POST" action="{{ route('profile.addresses.default', $addr->id_alamat) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline btn-sm">Jadikan Utama</button>
                                </form>
                            @endif
                            <a href="{{ route('profile.addresses.edit', $addr->id_alamat) }}" class="btn btn-outline btn-sm">Edit</a>
                            <form method="POST" action="{{ route('profile.addresses.delete', $addr->id_alamat) }}"
                                  onsubmit="return confirm('Hapus alamat ini? Riwayat pesanan tetap tersimpan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="address-empty">
                        <p>Belum ada alamat tersimpan. Tambahkan alamat untuk memudahkan checkout.</p>
                    </div>
                @endforelse

                @if($addresses->isEmpty())
                    <div class="form-card" id="form-alamat">
                        <h2>Lengkapi Alamat Pengiriman</h2>
                        <form method="POST" action="{{ route('profile.addresses.store') }}">
                            @csrf
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                                <div class="form-group">
                                    <label for="nama_penerima">Nama Penerima</label>
                                    <input type="text" id="nama_penerima" name="nama_penerima" value="{{ old('nama_penerima', auth()->user()->nama) }}" required>
                                    @error('nama_penerima') <div class="form-error">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="no_hp">No. HP</label>
                                    <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', auth()->user()->no_hp) }}" required>
                                    @error('no_hp') <div class="form-error">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="alamat_lengkap">Alamat Lengkap</label>
                                <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3" required>{{ old('alamat_lengkap') }}</textarea>
                                @error('alamat_lengkap') <div class="form-error">{{ $message }}</div> @enderror
                            </div>
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                                <div class="form-group">
                                    <label for="kota">Kota</label>
                                    <input type="text" id="kota" name="kota" value="{{ old('kota') }}" required>
                                    @error('kota') <div class="form-error">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group">
                                    <label for="kode_pos">Kode Pos</label>
                                    <input type="text" id="kode_pos" name="kode_pos" value="{{ old('kode_pos') }}">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Simpan Alamat</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </main>

    @include('layout.partials.footer')
</body>
</html>
