<?php

namespace App\Support;

use App\Models\CartItem;
use App\Models\Variant;

class CartStorage
{
    /**
     * @return array<int, array{id_varian: int, jumlah: int}>
     */
    public static function rowsForUser(int $userId): array
    {
        return CartItem::query()
            ->where('id_user', $userId)
            ->orderBy('id_keranjang')
            ->get(['id_varian', 'jumlah'])
            ->map(fn (CartItem $item) => [
                'id_varian' => (int) $item->id_varian,
                'jumlah' => (int) $item->jumlah,
            ])
            ->all();
    }

    /**
     * @param  array<int, array{id_varian: int, jumlah: int}>  $cart
     * @return array<int, array{id_varian: int, jumlah: int}>
     */
    public static function replaceForUser(int $userId, array $cart): array
    {
        $quantities = self::existingQuantities($cart);

        $query = CartItem::where('id_user', $userId);

        if ($quantities !== []) {
            $query->whereNotIn('id_varian', array_keys($quantities));
        }

        $query->delete();

        foreach ($quantities as $variantId => $quantity) {
            CartItem::updateOrCreate(
                [
                    'id_user' => $userId,
                    'id_varian' => $variantId,
                ],
                ['jumlah' => $quantity]
            );
        }

        return self::toRows($quantities);
    }

    /**
     * @param  array<int, array{id_varian: int, jumlah: int}>  $sessionCart
     * @return array<int, array{id_varian: int, jumlah: int}>
     */
    public static function mergeForUser(int $userId, array $sessionCart): array
    {
        $quantities = [];

        foreach (self::rowsForUser($userId) as $row) {
            $quantities[$row['id_varian']] = $row['jumlah'];
        }

        foreach ($sessionCart as $row) {
            $variantId = (int) ($row['id_varian'] ?? 0);
            $quantity = (int) ($row['jumlah'] ?? 0);

            if ($variantId <= 0 || $quantity <= 0) {
                continue;
            }

            $quantities[$variantId] = ($quantities[$variantId] ?? 0) + $quantity;
        }

        return self::replaceForUser($userId, self::toRows($quantities));
    }

    public static function clearForUser(int $userId): void
    {
        CartItem::where('id_user', $userId)->delete();
    }

    /**
     * @param  array<int, array{id_varian: int, jumlah: int}>  $cart
     * @return array<int, int>
     */
    private static function existingQuantities(array $cart): array
    {
        $quantities = [];

        foreach ($cart as $row) {
            $variantId = (int) ($row['id_varian'] ?? 0);
            $quantity = (int) ($row['jumlah'] ?? 0);

            if ($variantId <= 0 || $quantity <= 0) {
                continue;
            }

            $quantities[$variantId] = ($quantities[$variantId] ?? 0) + $quantity;
        }

        if ($quantities === []) {
            return [];
        }

        $validIds = Variant::whereIn('id_varian', array_keys($quantities))
            ->pluck('id_varian')
            ->map(fn ($id) => (int) $id)
            ->all();

        return array_intersect_key($quantities, array_flip($validIds));
    }

    /**
     * @param  array<int, int>  $quantities
     * @return array<int, array{id_varian: int, jumlah: int}>
     */
    private static function toRows(array $quantities): array
    {
        $rows = [];

        foreach ($quantities as $variantId => $quantity) {
            $rows[] = [
                'id_varian' => $variantId,
                'jumlah' => $quantity,
            ];
        }

        return $rows;
    }
}
