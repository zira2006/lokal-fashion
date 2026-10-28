
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungkan Pembayaran | Lokal Fashion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lokal-fashion.css') }}">
    <style>
        .connect-page { padding: 35px 0 70px; }
        .connect-title { font-family: 'Playfair Display', serif; font-size: 28px; color: var(--dark); margin: 0 0 8px; }
        .connect-subtitle { font-size: 13px; color: var(--muted); margin: 0 0 28px; }
        .connect-card { max-width: 620px; background: var(--white); border: 1px solid var(--line); border-radius: 14px; padding: 28px; }
        .connect-provider { display: flex; align-items: center; justify-content: space-between; gap: 15px; padding: 16px 18px; margin-bottom: 24px; border: 1px solid var(--line); border-radius: 12px; background: #fdf9f4; }
        .connect-provider-label { font-size: 12px; color: var(--muted); margin-bottom: 3px; }
        .connect-provider-name { font-size: 16px; font-weight: 700; color: var(--dark); }
        .connect-badge { font-size: 10px; font-weight: 700; padding: 4px 10px; border-radius: 999px; background: #fdf0d5; color: #8a6d1a; white-space: nowrap; }
        .connect-badge.connected { background: #d9edda; color: #3d6b3e; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: var(--dark); margin-bottom: 7px; }
        .form-group input { width: 100%; padding: 12px 15px; border: 1px solid var(--line); border-radius: 10px; font-size: 14px; font-family: inherit; background: #fff; outline: none; transition: border-color 0.2s; }
        .form-group input:focus { border-color: var(--brown); }
        .form-error { color: #a8442e; font-size: 12px; margin-top: 5px; }
        .connect-note { font-size: 12px; color: var(--muted); line-height: 1.6; margin: 0 0 22px; }
        .connect-actions { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .btn { display: inline-block; padding: 12px 26px; border: none; border-radius: 25px; font-size: 14px; font-weight: 600; font-family: inherit; cursor: pointer; text-decoration: none; text-align: center; transition: all 0.2s; }
        .btn-primary { background: var(--dark); color: #fff; }
        .btn-primary:hover { background: #6d5340; }
        .btn-outline { background: transparent; border: 1.5px solid var(--dark); color: var(--dark); }
        .btn-outline:hover { background: var(--dark); color: #fff; }
    </style>
</head>
<body>
    @include('layout.partials.header')

    <main class="container connect-page">
        <h1 class="connect-title">Hubungkan {{ $type === 'bank' ? 'Rekening Bank' : 'E-Wallet' }}</h1>
        <p class="connect-subtitle">Hubungkan akun pembayaran untuk pesanan {{ $order->no_invoice }}</p>

        <div class="connect-card">
            <div class="connect-provider">
                <div>
                    <div class="connect-provider-label">{{ $type === 'bank' ? 'Bank' : 'E-Wallet' }}</div>
                    <div class="connect-provider-name">{{ $provider }}</div>
                </div>
                <span class="connect-badge {{ $account ? 'connected' : '' }}">
                    {{ $account ? 'Sudah tersambung' : 'Belum tersambung' }}
                </span>
            </div>

            <p class="connect-note">
                Data ini disimpan untuk menandai metode pembayaran di invoice.
                Pastikan nomor dan nama pemilik akun sudah benar.
            </p>

            <form method="POST" action="{{ route('payment.connect.store', $order->no_invoice) }}">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">
                <input type="hidden" name="provider" value="{{ $provider }}">

                <div class="form-group">
                    <label for="account_number">{{ $type === 'bank' ? 'Nomor Rekening' : 'Nomor HP / Akun E-Wallet' }}</label>
                    <input type="text" id="account_number" name="account_number"
                           value="{{ old('account_number', $account->account_number ?? '') }}"
                           placeholder="{{ $type === 'bank' ? 'Contoh: 1234567890' : 'Contoh: 081234567890' }}"
                           maxlength="50" required>
                    @error('account_number') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="account_name">{{ $type === 'bank' ? 'Nama Pemilik Rekening' : 'Nama Pemilik Akun' }}</label>
                    <input type="text" id="account_name" name="account_name"
                           value="{{ old('account_name', $account->account_name ?? auth()->user()->nama) }}"
                           maxlength="100" required>
                    @error('account_name') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                @error('provider')
                    <p class="form-error">{{ $message }}</p>
                @enderror

                <div class="connect-actions">
                    <button type="submit" class="btn btn-primary">
                        {{ $account ? 'Perbarui Koneksi' : 'Hubungkan Sekarang' }}
                    </button>
                    <a href="{{ route('payment.show', ['invoice' => $order->no_invoice, 'method' => $type, 'provider' => $provider]) }}" class="btn btn-outline">Batal</a>
                </div>
            </form>
        </div>
    </main>

    @include('layout.partials.footer')
</body>
</html>
