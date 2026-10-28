
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran {{ $order->no_invoice }} | Lokal Fashion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lokal-fashion.css') }}">
    <style>
        .payment-page { padding: 35px 0 70px; }
        .payment-title { font-family: 'Playfair Display', serif; font-size: 28px; color: var(--dark); margin: 0 0 30px; }
        .payment-grid { display: grid; grid-template-columns: 1fr 380px; gap: 35px; align-items: start; }
        .payment-card { background: var(--white); border: 1px solid var(--line); border-radius: 14px; padding: 28px; margin-bottom: 22px; }
        .payment-card h2 { font-family: 'Playfair Display', serif; font-size: 19px; color: var(--dark); margin: 0 0 20px; }

        .payment-method { border: 1.5px solid var(--line); border-radius: 12px; padding: 16px 20px; margin-bottom: 12px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 14px; }
        .payment-method:hover { border-color: var(--brown); }
        .payment-method.selected { border-color: var(--dark); background: #fdf6ee; }
        .payment-method input[type="radio"] { accent-color: var(--brown); width: 16px; height: 16px; flex-shrink: 0; }
        .payment-method-main { display: flex; flex-direction: column; gap: 2px; }
        .payment-method-label { font-size: 14px; font-weight: 600; color: var(--dark); }
        .payment-method-desc { font-size: 12px; color: var(--muted); }

        .payment-account-panel { margin-top: 18px; padding: 18px; border: 1px solid var(--line); border-radius: 12px; background: #fdf9f4; }
        .payment-account-label { display: block; font-size: 12px; font-weight: 600; color: var(--dark); margin-bottom: 7px; }
        .payment-account-panel select { width: 100%; padding: 12px 14px; border: 1px solid var(--line); border-radius: 10px; background: #fff; font-family: inherit; font-size: 14px; color: var(--text); outline: none; }
        .payment-account-panel select:focus { border-color: var(--brown); }
        .connection-status { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 12px; font-size: 12px; color: var(--muted); line-height: 1.5; }
        .connection-badge { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 999px; font-size: 10px; font-weight: 700; letter-spacing: 0.02em; }
        .connection-badge.connected { background: #d9edda; color: #3d6b3e; }
        .connection-badge.unconnected { background: #fdf0d5; color: #8a6d1a; }
        .connect-link { display: inline-flex; margin-top: 10px; font-size: 12px; font-weight: 700; color: var(--brown); text-decoration: none; }
        .connect-link:hover { color: var(--dark); text-decoration: underline; }

        .invoice-card { background: var(--white); border: 1px solid var(--line); border-radius: 14px; padding: 28px; }
        .invoice-card h2 { font-family: 'Playfair Display', serif; font-size: 19px; color: var(--dark); margin: 0 0 20px; }
        .invoice-row { display: flex; justify-content: space-between; gap: 15px; font-size: 14px; padding: 8px 0; color: var(--text); }
        .invoice-row.total { border-top: 1px solid var(--line); margin-top: 10px; padding-top: 14px; font-weight: 700; font-size: 17px; color: var(--dark); }
        .invoice-row .label { color: var(--muted); }

        .btn { display: inline-block; padding: 13px 28px; border: none; border-radius: 25px; font-size: 14px; font-weight: 600; font-family: inherit; cursor: pointer; text-decoration: none; text-align: center; transition: all 0.2s; }
        .btn-primary { background: var(--dark); color: #fff; }
        .btn-primary:hover { background: #6d5340; }
        .btn-outline { background: transparent; border: 1.5px solid var(--dark); color: var(--dark); }
        .btn-outline:hover { background: var(--dark); color: #fff; }
        .btn-block { width: 100%; }
        .btn:disabled { opacity: 0.55; cursor: not-allowed; }
        .payment-note { font-size: 12px; color: var(--muted); margin-top: 16px; line-height: 1.6; }
        .payment-warning { background: #fff8e8; border: 1px solid #ecd6a8; border-radius: 12px; padding: 14px 16px; margin-bottom: 16px; font-size: 12px; color: #75591c; line-height: 1.65; }
        .payment-warning strong { color: #5f470f; }
        .form-error { color: #a8442e; font-size: 12px; margin-top: 8px; }

        .status-badge { display: inline-block; padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status-menunggu { background: #fdf0d5; color: #8a6d1a; }
        .status-lunas { background: #d9edda; color: #3d6b3e; }

        @media (max-width: 900px) {
            .payment-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    @include('layout.partials.header')

    @php
        $selectedAccount = $connectedAccounts[$selectedMethod.':'.$selectedProvider] ?? null;
    @endphp

    <main class="container payment-page">
        <h1 class="payment-title">Pembayaran</h1>

        @if($providerNotFound)
            <div class="payment-warning">
                <strong>Provider tidak ditemukan.</strong>
                {{ $requestedProvider }} tidak terdaftar di aplikasi. Status:
                <strong>tidak terhubung</strong>. Silakan pilih bank atau e-wallet yang tersedia.
            </div>
        @endif

        @if($unsupportedAccounts->isNotEmpty())
            <div class="payment-warning">
                <strong>Akun tidak terhubung.</strong>
                Provider berikut tidak ditemukan di aplikasi:
                {{ $unsupportedAccounts->map(fn ($account) => $account->provider.' ('.$account->type_label.')')->join(', ') }}.
                Akun ini tidak dapat dipakai untuk pembayaran.
            </div>
        @endif

        <div class="payment-grid">
            <div>
                <div class="payment-card">
                    <h2>Pilih Metode Pembayaran</h2>

                    <form method="POST" action="{{ route('payment.pay', $order->no_invoice) }}" id="paymentForm">
                        @csrf

                        <label class="payment-method {{ $selectedMethod === 'bank' ? 'selected' : '' }}">
                            <input type="radio" name="metode_pembayaran" value="bank" @checked($selectedMethod === 'bank')>
                            <span class="payment-method-main">
                                <span class="payment-method-label">Transfer Bank</span>
                                <span class="payment-method-desc">{{ implode(', ', $providers['bank'] ?? []) }}</span>
                            </span>
                        </label>

                        <label class="payment-method {{ $selectedMethod === 'ewallet' ? 'selected' : '' }}">
                            <input type="radio" name="metode_pembayaran" value="ewallet" @checked($selectedMethod === 'ewallet')>
                            <span class="payment-method-main">
                                <span class="payment-method-label">E-Wallet</span>
                                <span class="payment-method-desc">{{ implode(', ', $providers['ewallet'] ?? []) }}</span>
                            </span>
                        </label>

                        <label class="payment-method {{ $selectedMethod === 'cod' ? 'selected' : '' }}">
                            <input type="radio" name="metode_pembayaran" value="cod" @checked($selectedMethod === 'cod')>
                            <span class="payment-method-main">
                                <span class="payment-method-label">Bayar di Tempat (COD)</span>
                                <span class="payment-method-desc">Bayar saat pesanan diterima</span>
                            </span>
                        </label>

                        <div class="payment-account-panel" id="accountPanel" @if($selectedMethod === 'cod') style="display:none;" @endif>
                            <label for="paymentProvider" class="payment-account-label" id="providerLabel">Pilih bank</label>
                            <select name="payment_provider" id="paymentProvider">
                                @foreach($providers[$selectedMethod] ?? [] as $provider)
                                    <option value="{{ $provider }}" @selected($selectedProvider === $provider)>{{ $provider }}</option>
                                @endforeach
                            </select>
                            <div class="connection-status" id="connectionStatus">
                                @if($selectedAccount)
                                    <span class="connection-badge connected">Terhubung</span>
                                    <span>{{ $selectedProvider }} • {{ $selectedAccount['masked'] }} a.n. {{ $selectedAccount['name'] }}</span>
                                @elseif($selectedMethod !== 'cod')
                                    <span class="connection-badge unconnected">Tidak terhubung</span>
                                    <span>
                                        @if($selectedProvider)
                                            Hubungkan {{ $selectedMethod === 'bank' ? 'rekening' : 'e-wallet' }} {{ $selectedProvider }} dulu untuk melanjutkan.
                                        @else
                                            Belum ada bank atau e-wallet yang terdaftar di aplikasi.
                                        @endif
                                    </span>
                                @endif
                            </div>
                            <a href="{{ $selectedProvider ? route('payment.connect.form', ['invoice' => $order->no_invoice, 'type' => $selectedMethod, 'provider' => $selectedProvider]) : '#' }}"
                               class="connect-link" id="connectLink"
                               @if(! $selectedProvider || $selectedMethod === 'cod') style="display:none;" @endif>
                                {{ $selectedAccount ? 'Ubah koneksi' : 'Hubungkan sekarang' }}
                            </a>
                        </div>

                        @error('payment_provider')
                            <p class="form-error">{{ $message }}</p>
                        @enderror

                        @error('metode_pembayaran')
                            <p class="form-error">{{ $message }}</p>
                        @enderror

                        <button type="submit" class="btn btn-primary btn-block" id="payButton" style="margin-top: 20px;"
                            @if($selectedMethod !== 'cod' && ! $selectedAccount) disabled @endif>Bayar Sekarang</button>
                    </form>

                    <p class="payment-note">
                        Untuk transfer bank dan e-wallet, provider harus terdaftar di aplikasi dan
                        rekening atau akun harus dihubungkan terlebih dahulu. Jika provider tidak
                        ditemukan, statusnya tidak terhubung dan pembayaran tidak dapat dilanjutkan.
                        Pesanan baru akan muncul di Pesanan Saya setelah kamu menekan
                        <strong>Bayar Sekarang</strong>. Setelah pesanan dibuat, status pesanan
                        akan berubah menjadi "diproses" dan metode pembayaran tercatat di invoice.
                        Khusus COD, status pembayaran berubah menjadi <strong>Lunas</strong> setelah
                        barang diterima dan dikonfirmasi.
                    </p>
                </div>
            </div>

            <div class="invoice-card">
                <h2>Invoice</h2>
                <div class="invoice-row">
                    <span class="label">No. Invoice</span>
                    <span><strong>{{ $order->no_invoice }}</strong></span>
                </div>
                <div class="invoice-row">
                    <span class="label">Status</span>
                    <span class="status-badge {{ $order->status_pembayaran == 'lunas' ? 'status-lunas' : 'status-menunggu' }}">
                        {{ $order->status_pembayaran == 'lunas' ? 'Lunas' : 'Menunggu' }}
                    </span>
                </div>
                <div class="invoice-row">
                    <span class="label">Metode Pembayaran</span>
                    <span id="invoiceMethod">{{ $methods[$selectedMethod] ?? 'Belum dipilih' }}{{ $selectedProvider ? ' '.$selectedProvider : '' }}</span>
                </div>
                <div class="invoice-row" id="invoiceAccountRow" @if($selectedMethod === 'cod') style="display:none;" @endif>
                    <span class="label">Akun</span>
                    <span id="invoiceAccount">{{ $selectedAccount['masked'] ?? 'Tidak terhubung' }}</span>
                </div>

                <div style="border-top: 1px dashed var(--line); margin: 14px 0;"></div>

                @foreach($order->items as $item)
                    <div class="invoice-row">
                        <span>{{ $item->variant->product->nama_produk }} ({{ $item->variant->warna }}, {{ $item->variant->ukuran }}) x{{ $item->jumlah }}</span>
                        <span>Rp{{ number_format((float) $item->harga * $item->jumlah, 0, ',', '.') }}</span>
                    </div>
                @endforeach

                <div class="invoice-row total">
                    <span>Total Bayar</span>
                    <span>Rp{{ number_format((float) $order->total_harga, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </main>

    @include('layout.partials.footer')

    <script>
        const providers = @json($providers);
        const connectedAccounts = @json($connectedAccounts);
        const methodLabels = @json($methods);
        const connectFormUrl = @json(route('payment.connect.form', $order->no_invoice));

        const providerSelect = document.getElementById('paymentProvider');
        const accountPanel = document.getElementById('accountPanel');
        const providerLabel = document.getElementById('providerLabel');
        const connectionStatus = document.getElementById('connectionStatus');
        const connectLink = document.getElementById('connectLink');
        const payButton = document.getElementById('payButton');
        const invoiceMethod = document.getElementById('invoiceMethod');
        const invoiceAccount = document.getElementById('invoiceAccount');
        const invoiceAccountRow = document.getElementById('invoiceAccountRow');

        function selectedMethod() {
            return document.querySelector('input[name="metode_pembayaran"]:checked')?.value || 'bank';
        }

        function renderProviders(method, preferred) {
            providerSelect.innerHTML = '';

            if (!providers[method]) {
                return;
            }

            providers[method].forEach(provider => {
                const option = document.createElement('option');
                option.value = provider;
                option.textContent = provider;
                providerSelect.appendChild(option);
            });

            if (preferred && providers[method].includes(preferred)) {
                providerSelect.value = preferred;
            }
        }

        function setConnectLink(method, provider) {
            if (!provider) {
                connectLink.style.display = 'none';
                return;
            }

            connectLink.href = connectFormUrl + '?type=' + encodeURIComponent(method) + '&provider=' + encodeURIComponent(provider);
            connectLink.style.display = 'inline-flex';
        }

        function updatePaymentState() {
            const method = selectedMethod();

            document.querySelectorAll('.payment-method').forEach(item => {
                item.classList.remove('selected');
            });
            document.querySelector('input[name="metode_pembayaran"]:checked')?.closest('.payment-method')?.classList.add('selected');

            if (method === 'cod') {
                accountPanel.style.display = 'none';
                payButton.disabled = false;
                invoiceMethod.textContent = methodLabels.cod;
                invoiceAccountRow.style.display = 'none';
                return;
            }

            accountPanel.style.display = 'block';
            providerLabel.textContent = method === 'bank' ? 'Pilih bank' : 'Pilih e-wallet';

            if (!providers[method]?.includes(providerSelect.value)) {
                renderProviders(method, providers[method]?.[0]);
            }

            const provider = providerSelect.value;
            const account = connectedAccounts[method + ':' + provider];

            invoiceMethod.textContent = methodLabels[method] + (provider ? ' ' + provider : '');
            invoiceAccountRow.style.display = 'flex';
            connectionStatus.innerHTML = '';

            if (!provider) {
                const badge = document.createElement('span');
                badge.className = 'connection-badge unconnected';
                badge.textContent = 'Tidak terhubung';

                const detail = document.createElement('span');
                detail.textContent = 'Belum ada bank atau e-wallet yang terdaftar di aplikasi.';

                connectionStatus.append(badge, detail);
                invoiceAccount.textContent = 'Tidak terhubung';
                connectLink.style.display = 'none';
                payButton.disabled = true;

                return;
            }

            if (account) {
                const badge = document.createElement('span');
                badge.className = 'connection-badge connected';
                badge.textContent = 'Terhubung';

                const detail = document.createElement('span');
                detail.textContent = provider + ' • ' + account.masked + ' a.n. ' + account.name;

                connectionStatus.append(badge, detail);
                invoiceAccount.textContent = account.masked;
                connectLink.textContent = 'Ubah koneksi';
                payButton.disabled = false;
            } else {
                const badge = document.createElement('span');
                badge.className = 'connection-badge unconnected';
                badge.textContent = 'Tidak terhubung';

                const detail = document.createElement('span');
                detail.textContent = 'Hubungkan ' + (method === 'bank' ? 'rekening' : 'e-wallet') + ' ' + provider + ' dulu untuk melanjutkan.';

                connectionStatus.append(badge, detail);
                invoiceAccount.textContent = 'Tidak terhubung';
                connectLink.textContent = 'Hubungkan sekarang';
                payButton.disabled = true;
            }

            setConnectLink(method, provider);
        }

        document.querySelectorAll('input[name="metode_pembayaran"]').forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value !== 'cod') {
                    renderProviders(this.value, providers[this.value]?.[0]);
                }
                updatePaymentState();
            });
        });

        providerSelect.addEventListener('change', updatePaymentState);

        renderProviders(selectedMethod(), @json($selectedProvider));
        updatePaymentState();
    </script>
</body>
</html>
