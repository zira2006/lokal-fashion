<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Selaraskan skema database dengan ERD marketplace.
     *
     * Nama tabel utama tetap dipertahankan agar seluruh aplikasi yang sudah
     * berjalan tidak perlu dirombak. Kolom dan relasi yang belum ada
     * ditambahkan, lalu data lama diisi otomatis.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nama', 100)->nullable();
            $table->string('password_hash')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp', 20)->nullable();
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->renameColumn('nama_admin', 'nama');
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->string('email')->nullable();
            $table->string('password_hash')->nullable();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('id_admin')->nullable();
            $table->index('id_admin');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('sku', 100)->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('no_invoice', 30)->nullable();
            $table->unsignedBigInteger('id_varian')->nullable();
            $table->dateTime('tgl_pesanan')->nullable();
            $table->decimal('harga_satuan', 12, 2)->nullable();
            $table->integer('jumlah')->nullable();
            $table->decimal('total_harga', 12, 2)->nullable();
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dateTime('tgl')->nullable();
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->renameColumn('id', 'id_keranjang');
        });

        Schema::create('registrations', function (Blueprint $table) {
            $table->id('id_registrasi');
            $table->unsignedBigInteger('id_user');
            $table->dateTime('tgl_registrasi')->nullable();
            $table->string('status_registrasi', 30)->default('menunggu');
            $table->string('dokumen_identitas')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->timestamps();

            $table->foreign('id_user')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->unique('id_user');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id('id_bayar');
            $table->unsignedBigInteger('id_pesanan');
            $table->dateTime('tgl_bayar')->nullable();
            $table->string('metode', 30)->nullable();
            $table->decimal('jumlah', 12, 2)->default(0);
            $table->string('status', 30)->default('menunggu');
            $table->string('provider', 50)->nullable();
            $table->string('account', 100)->nullable();
            $table->timestamps();

            $table->foreign('id_pesanan')
                ->references('id_pesanan')
                ->on('orders')
                ->cascadeOnDelete();

            $table->unique('id_pesanan');
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->id('id_kirim');
            $table->unsignedBigInteger('id_pesanan');
            $table->string('kurir', 50)->nullable();
            $table->string('no_resi', 100)->nullable();
            $table->string('status', 30)->default('menunggu');
            $table->timestamps();

            $table->foreign('id_pesanan')
                ->references('id_pesanan')
                ->on('orders')
                ->cascadeOnDelete();

            $table->unique('id_pesanan');
        });

        $this->backfillData();
        $this->addErdIndexes();
    }

    private function backfillData(): void
    {
        // Data user lama dipetakan ke nama kolom ERD.
        DB::statement('UPDATE users SET nama = name, no_hp = phone, password_hash = password');

        DB::statement(<<<'SQL'
UPDATE users SET alamat = (
    SELECT alamat_lengkap
    FROM addresses
    WHERE addresses.id_user = users.id
      AND addresses.deleted_at IS NULL
    ORDER BY is_default DESC, id_alamat ASC
    LIMIT 1
)
SQL);

        $this->backfillAdmins();
        $this->backfillCategories();
        $this->backfillVariants();
        $this->backfillOrders();
        $this->backfillPaymentsAndShipments();
        $this->backfillRegistrations();
        $this->backfillReviews();
    }

    private function backfillAdmins(): void
    {
        $adminUsers = DB::table('users')->where('role', 'admin')->get();

        foreach ($adminUsers as $user) {
            $data = [
                'nama' => $user->name,
                'email' => $user->email,
                'password_hash' => $user->password,
                'updated_at' => $user->updated_at ?? now(),
            ];

            $existing = DB::table('admins')->where('id_user', $user->id)->first();

            if ($existing) {
                DB::table('admins')
                    ->where('id_admin', $existing->id_admin)
                    ->update($data);

                continue;
            }

            DB::table('admins')->insert($data + [
                'id_user' => $user->id,
                'created_at' => $user->created_at ?? now(),
            ]);
        }
    }

    private function backfillCategories(): void
    {
        $adminId = DB::table('admins')->orderBy('id_admin')->value('id_admin');

        if ($adminId) {
            DB::table('categories')
                ->whereNull('id_admin')
                ->update(['id_admin' => $adminId]);
        }
    }

    private function backfillVariants(): void
    {
        DB::statement(<<<'SQL'
UPDATE product_variants
SET sku = 'SKU-' || id_produk || '-' || id_varian
WHERE sku IS NULL OR sku = ''
SQL);
    }

    private function backfillOrders(): void
    {
        DB::statement(<<<'SQL'
UPDATE orders
SET no_invoice = invoice,
    total_harga = total,
    tgl_pesanan = created_at
SQL);

        // Kolom ringkasan item pada ERD diisi dari item pesanan pertama.
        // Detail lengkap tetap tersimpan di order_items untuk pesanan multi-item.
        DB::statement(<<<'SQL'
UPDATE orders
SET id_varian = (
        SELECT id_varian FROM order_items
        WHERE order_items.id_pesanan = orders.id_pesanan
        ORDER BY id_item ASC LIMIT 1
    ),
    harga_satuan = (
        SELECT harga FROM order_items
        WHERE order_items.id_pesanan = orders.id_pesanan
        ORDER BY id_item ASC LIMIT 1
    ),
    jumlah = (
        SELECT jumlah FROM order_items
        WHERE order_items.id_pesanan = orders.id_pesanan
        ORDER BY id_item ASC LIMIT 1
    )
WHERE EXISTS (
    SELECT 1 FROM order_items
    WHERE order_items.id_pesanan = orders.id_pesanan
)
SQL);
    }

    private function backfillPaymentsAndShipments(): void
    {
        foreach (DB::table('orders')->orderBy('id_pesanan')->get() as $order) {
            if (! DB::table('payments')->where('id_pesanan', $order->id_pesanan)->exists()) {
                DB::table('payments')->insert([
                    'id_pesanan' => $order->id_pesanan,
                    'tgl_bayar' => $order->status_pembayaran === 'lunas'
                        ? ($order->updated_at ?? $order->created_at ?? now())
                        : null,
                    'metode' => $order->metode_pembayaran,
                    'jumlah' => $order->total ?? 0,
                    'status' => $order->status_pembayaran ?: 'menunggu',
                    'provider' => $order->payment_provider,
                    'account' => $order->payment_account,
                    'created_at' => $order->created_at ?? now(),
                    'updated_at' => $order->updated_at ?? now(),
                ]);
            }

            if (! DB::table('shipments')->where('id_pesanan', $order->id_pesanan)->exists()) {
                DB::table('shipments')->insert([
                    'id_pesanan' => $order->id_pesanan,
                    'kurir' => $order->kurir,
                    'no_resi' => $order->resi,
                    'status' => $this->shipmentStatus($order->status),
                    'created_at' => $order->created_at ?? now(),
                    'updated_at' => $order->updated_at ?? now(),
                ]);
            }
        }
    }

    private function backfillRegistrations(): void
    {
        $sellers = DB::table('users')->where('role', 'penjual')->get();

        foreach ($sellers as $seller) {
            if (DB::table('registrations')->where('id_user', $seller->id)->exists()) {
                continue;
            }

            $store = DB::table('stores')
                ->where('id_user', $seller->id)
                ->orderBy('id_toko')
                ->first();

            DB::table('registrations')->insert([
                'id_user' => $seller->id,
                'tgl_registrasi' => $store->created_at ?? $seller->created_at ?? now(),
                'status_registrasi' => $store->status_verifikasi ?? 'menunggu',
                'dokumen_identitas' => null,
                'catatan_admin' => null,
                'created_at' => $store->created_at ?? $seller->created_at ?? now(),
                'updated_at' => $store->updated_at ?? $seller->updated_at ?? now(),
            ]);
        }
    }

    private function backfillReviews(): void
    {
        DB::statement('UPDATE reviews SET tgl = created_at WHERE tgl IS NULL');
    }

    private function addErdIndexes(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->unique('sku');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unique('no_invoice');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->unique('id_pesanan');
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->unique('email');
        });
    }

    private function shipmentStatus(?string $orderStatus): string
    {
        return match ($orderStatus) {
            'baru' => 'menunggu',
            'diproses' => 'diproses',
            'dikirim' => 'dikirim',
            'selesai' => 'diterima',
            'batal' => 'dibatalkan',
            default => $orderStatus ?: 'menunggu',
        };
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropUnique(['email']);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique(['id_pesanan']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['no_invoice']);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropUnique(['sku']);
        });

        Schema::dropIfExists('shipments');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('registrations');

        Schema::table('cart_items', function (Blueprint $table) {
            $table->renameColumn('id_keranjang', 'id');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('tgl');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'no_invoice',
                'id_varian',
                'tgl_pesanan',
                'harga_satuan',
                'jumlah',
                'total_harga',
            ]);
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('sku');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['id_admin']);
            $table->dropColumn('id_admin');
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn(['email', 'password_hash']);
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->renameColumn('nama', 'nama_admin');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nama', 'password_hash', 'alamat', 'no_hp']);
        });
    }
};
