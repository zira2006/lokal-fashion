<?php

namespace App\Support;

class PaymentProviders
{
    /**
     * @return array<string, string>
     */
    public static function methods(): array
    {
        return config('payments.methods', []);
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function all(): array
    {
        return config('payments.providers', []);
    }

    /**
     * @return array<int, string>
     */
    public static function forMethod(string $method): array
    {
        return self::all()[$method] ?? [];
    }

    public static function isKnownMethod(string $method): bool
    {
        return array_key_exists($method, self::methods());
    }

    public static function exists(string $type, string $provider): bool
    {
        if ($provider === '') {
            return false;
        }

        return in_array($provider, self::forMethod($type), true);
    }
}
