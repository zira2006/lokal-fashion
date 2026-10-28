<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentAccount;
use App\Support\PaymentProviders;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function show(Request $request, string $invoice): View|RedirectResponse
    {
        $order = $this->orderForUser($invoice);

        if ($redirect = $this->redirectIfAlreadyPlaced($order)) {
            return $redirect;
        }

        $paymentAccounts = PaymentAccount::where('id_user', Auth::id())->get();

        $supportedAccounts = $paymentAccounts->filter(
            fn (PaymentAccount $account) => PaymentProviders::exists($account->type, $account->provider)
        );

        $unsupportedAccounts = $paymentAccounts->reject(
            fn (PaymentAccount $account) => PaymentProviders::exists($account->type, $account->provider)
        );

        $connectedAccounts = $supportedAccounts->mapWithKeys(fn (PaymentAccount $account) => [
            $account->type.':'.$account->provider => [
                'masked' => $account->masked_account_number,
                'name' => $account->account_name,
            ],
        ]);

        $providers = PaymentProviders::all();
        $methods = PaymentProviders::methods();

        $selectedMethod = (string) $request->query('method', $request->old('metode_pembayaran', 'bank'));
        if (! PaymentProviders::isKnownMethod($selectedMethod)) {
            $selectedMethod = 'bank';
        }

        $requestedProvider = trim((string) $request->query('provider', $request->old('payment_provider', '')));
        $providerNotFound = false;
        $selectedProvider = null;

        if (in_array($selectedMethod, ['bank', 'ewallet'], true)) {
            $availableProviders = PaymentProviders::forMethod($selectedMethod);

            if ($requestedProvider !== '' && ! in_array($requestedProvider, $availableProviders, true)) {
                $providerNotFound = true;
            }

            $selectedProvider = in_array($requestedProvider, $availableProviders, true)
                ? $requestedProvider
                : ($availableProviders[0] ?? null);
        }

        return view('storefront.payment', compact(
            'order',
            'providers',
            'methods',
            'connectedAccounts',
            'selectedMethod',
            'selectedProvider',
            'unsupportedAccounts',
            'providerNotFound',
            'requestedProvider'
        ));
    }

    public function connectForm(Request $request, string $invoice): View|RedirectResponse
    {
        $order = $this->orderForUser($invoice);

        if ($redirect = $this->redirectIfAlreadyPlaced($order)) {
            return $redirect;
        }

        $type = (string) $request->query('type');
        $provider = (string) $request->query('provider');

        abort_unless(PaymentProviders::exists($type, $provider), 404);

        $account = PaymentAccount::where('id_user', Auth::id())
            ->where('type', $type)
            ->where('provider', $provider)
            ->first();

        return view('storefront.payment-connect', compact('order', 'type', 'provider', 'account'));
    }

    public function storeConnection(Request $request, string $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(array_keys(PaymentProviders::all()))],
            'provider' => ['required', 'string', 'max:50'],
            'account_number' => ['required', 'string', 'max:50'],
            'account_name' => ['required', 'string', 'max:100'],
        ]);

        if (! PaymentProviders::exists($validated['type'], $validated['provider'])) {
            throw ValidationException::withMessages([
                'provider' => "Provider {$validated['provider']} tidak ditemukan di aplikasi. Status: tidak terhubung.",
            ]);
        }

        $order = $this->orderForUser($invoice);

        if ($redirect = $this->redirectIfAlreadyPlaced($order)) {
            return $redirect;
        }

        PaymentAccount::updateOrCreate(
            [
                'id_user' => Auth::id(),
                'type' => $validated['type'],
                'provider' => $validated['provider'],
            ],
            [
                'account_number' => $validated['account_number'],
                'account_name' => $validated['account_name'],
            ]
        );

        $label = $validated['type'] === 'bank' ? 'Rekening' : 'E-Wallet';

        return redirect()->route('payment.show', [
            'invoice' => $order->no_invoice,
            'method' => $validated['type'],
            'provider' => $validated['provider'],
        ])->with('success', "{$label} {$validated['provider']} berhasil dihubungkan.");
    }

    public function pay(Request $request, string $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'metode_pembayaran' => ['required', Rule::in(array_keys(PaymentProviders::methods()))],
            'payment_provider' => ['nullable', 'string', 'max:50'],
        ]);

        $order = $this->orderForUser($invoice);

        if ($redirect = $this->redirectIfAlreadyPlaced($order)) {
            return $redirect;
        }

        $account = null;
        $method = $validated['metode_pembayaran'];

        if (in_array($method, ['bank', 'ewallet'], true)) {
            $provider = $validated['payment_provider'] ?? null;

            if (! $provider || ! PaymentProviders::exists($method, $provider)) {
                $typeLabel = $method === 'bank' ? 'Bank' : 'E-wallet';

                throw ValidationException::withMessages([
                    'payment_provider' => "{$typeLabel} yang dipilih tidak ditemukan di aplikasi. Status: tidak terhubung.",
                ]);
            }

            $account = PaymentAccount::where('id_user', Auth::id())
                ->where('type', $method)
                ->where('provider', $provider)
                ->first();

            if (! $account) {
                $typeLabel = $method === 'bank' ? 'rekening' : 'e-wallet';

                throw ValidationException::withMessages([
                    'payment_provider' => "Hubungkan {$typeLabel} {$provider} terlebih dahulu sebelum membayar.",
                ]);
            }
        }

        $isCod = $method === 'cod';

        $order->update([
            'metode_pembayaran' => $method,
            'payment_provider' => $account?->provider,
            'payment_account' => $account?->masked_account_number,
            'status_pembayaran' => $isCod ? 'menunggu' : 'lunas',
            'status' => 'diproses',
        ]);

        if ($isCod) {
            return redirect()->route('orders.index')
                ->with('success', "Pesanan {$invoice} berhasil dibuat dengan Bayar di Tempat (COD). Pembayaran akan ditandai lunas setelah barang diterima.");
        }

        return redirect()->route('orders.index')
            ->with('success', "Pembayaran {$invoice} dengan {$order->paymentMethodLabel()} berhasil. Pesanan sedang diproses.");
    }

    private function redirectIfAlreadyPlaced(Order $order): ?RedirectResponse
    {
        if ($order->metode_pembayaran !== null || $order->status_pembayaran === 'lunas') {
            return redirect()->route('orders.index')->with('success', 'Pesanan sudah dibuat.');
        }

        return null;
    }

    private function orderForUser(string $invoice): Order
    {
        return Order::with(['address', 'items.variant.product'])
            ->where('no_invoice', $invoice)
            ->where('id_user', Auth::id())
            ->firstOrFail();
    }
}
