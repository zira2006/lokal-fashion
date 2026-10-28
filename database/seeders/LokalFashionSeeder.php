<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Registration;
use App\Models\Store;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LokalFashionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kategori
        $kategori = ['Batik', 'Tenun', 'Hijab', 'Tas', 'Aksesori'];
        foreach ($kategori as $nama) {
            Category::updateOrCreate(['nama_kategori' => $nama]);
        }

        $catBatik = Category::where('nama_kategori', 'Batik')->first();
        $catTenun = Category::where('nama_kategori', 'Tenun')->first();
        $catAtasan = Category::where('nama_kategori', 'Atasan')->first()
            ?? Category::create(['nama_kategori' => 'Atasan']);
        $catBawahan = Category::where('nama_kategori', 'Bawahan')->first()
            ?? Category::create(['nama_kategori' => 'Bawahan']);
        $catAksesoris = Category::where('nama_kategori', 'Aksesori')->first();

        // 2. Toko
        $storeBatikSari = Store::updateOrCreate(
            ['nama_toko' => 'Batik Sari'],
            [
                'deskripsi' => 'Batik tulis dan cap asli Indonesia.',
                'kota_asal' => 'Banda Aceh',
                'status_verifikasi' => 'terverifikasi',
            ]
        );

        $storeTenunJaya = Store::updateOrCreate(
            ['nama_toko' => 'Tenun Jaya'],
            [
                'deskripsi' => 'Kain tenun tradisional berkualitas.',
                'kota_asal' => 'Medan',
                'status_verifikasi' => 'terverifikasi',
            ]
        );

        $storeKebayaNusa = Store::updateOrCreate(
            ['nama_toko' => 'Kebaya Nusa'],
            [
                'deskripsi' => 'Kebaya modern dengan sentuhan tradisional.',
                'kota_asal' => 'Lhokseumawe',
                'status_verifikasi' => 'terverifikasi',
            ]
        );

        $storeRajutLokal = Store::updateOrCreate(
            ['nama_toko' => 'Rajut Lokal'],
            [
                'deskripsi' => 'Produk rajut handmade.',
                'kota_asal' => 'Banda Aceh',
                'status_verifikasi' => 'terverifikasi',
            ]
        );

        $storeLurikNusantara = Store::updateOrCreate(
            ['nama_toko' => 'Lurik Nusantara'],
            [
                'deskripsi' => 'Kain lurik asli Indonesia.',
                'kota_asal' => 'Medan',
                'status_verifikasi' => 'terverifikasi',
            ]
        );

        // 3. Produk & Varian (sesuai wireframe)
        $produk = [
            [
                'nama' => 'Kemeja Batik Tulis',
                'store' => $storeBatikSari,
                'kategori' => $catBatik,
                'harga' => 185000,
                'gambar' => 'images/products/kemeja-batik-tulis.jpg',
                'varian' => [
                    ['warna' => 'Merah', 'ukuran' => 'M', 'stok' => 10, 'harga' => 185000],
                    ['warna' => 'Biru', 'ukuran' => 'L', 'stok' => 8, 'harga' => 185000],
                    ['warna' => 'Hitam', 'ukuran' => 'S', 'stok' => 6, 'harga' => 185000],
                ],
            ],
            [
                'nama' => 'Rok Tenun Aceh',
                'store' => $storeTenunJaya,
                'kategori' => $catBawahan,
                'harga' => 150000,
                'gambar' => 'images/products/rok-tenun-aceh.jpg',
                'varian' => [
                    ['warna' => 'Biru', 'ukuran' => 'L', 'stok' => 9, 'harga' => 150000],
                ],
            ],
            [
                'nama' => 'Kebaya Modern',
                'store' => $storeKebayaNusa,
                'kategori' => $catAtasan,
                'harga' => 320000,
                'gambar' => 'images/products/kebaya-modern.jpg',
                'varian' => [
                    ['warna' => 'Merah', 'ukuran' => 'S', 'stok' => 5, 'harga' => 320000],
                    ['warna' => 'Hitam', 'ukuran' => 'M', 'stok' => 5, 'harga' => 320000],
                ],
            ],
            [
                'nama' => 'Tote Bag Rajut',
                'store' => $storeRajutLokal,
                'kategori' => $catAksesoris,
                'harga' => 95000,
                'gambar' => 'images/products/tote-bag-rajut.jpg',
                'varian' => [
                    ['warna' => 'Hitam', 'ukuran' => '-', 'stok' => 15, 'harga' => 95000],
                ],
            ],
            [
                'nama' => 'Syal Batik',
                'store' => $storeBatikSari,
                'kategori' => $catAksesoris,
                'harga' => 120000,
                'gambar' => 'images/products/syal-batik.jpg',
                'varian' => [
                    ['warna' => 'Merah', 'ukuran' => '-', 'stok' => 0, 'harga' => 120000],
                ],
            ],
            [
                'nama' => 'Kemeja Lurik',
                'store' => $storeLurikNusantara,
                'kategori' => $catAtasan,
                'harga' => 210000,
                'gambar' => 'images/products/kemeja-lurik.jpg',
                'varian' => [
                    ['warna' => 'Coklat', 'ukuran' => 'L', 'stok' => 7, 'harga' => 210000],
                ],
            ],
        ];

        foreach ($produk as $item) {
            $product = Product::updateOrCreate(
                ['nama_produk' => $item['nama']],
                [
                    'id_toko' => $item['store']->id_toko,
                    'id_kategori' => $item['kategori']->id_kategori,
                    'deskripsi' => $item['nama'].' — karya UMKM lokal berkualitas.',
                    'harga_dasar' => $item['harga'],
                    'status' => 'aktif',
                    'gambar' => $item['gambar'],
                ]
            );

            foreach ($item['varian'] as $v) {
                Variant::updateOrCreate(
                    [
                        'id_produk' => $product->id_produk,
                        'warna' => $v['warna'],
                        'ukuran' => $v['ukuran'],
                    ],
                    [
                        'stok' => $v['stok'],
                        'harga' => $v['harga'],
                    ]
                );
            }
        }

        // 4. User contoh (penjual)
        $penjual = User::updateOrCreate(
            ['email' => 'penjual@lokal.id'],
            [
                'nama' => 'Penjual Batik Sari',
                'password_hash' => Hash::make('password'),
                'role' => 'penjual',
                'no_hp' => '081234567890',
            ]
        );

        $storeBatikSari->update(['id_user' => $penjual->id_user]);

        Registration::updateOrCreate(
            ['id_user' => $penjual->id_user],
            [
                'tgl_registrasi' => $storeBatikSari->created_at ?? now(),
                'status_registrasi' => $storeBatikSari->status_verifikasi,
            ]
        );

        // 5. User contoh (pembeli)
        $pembeli = User::updateOrCreate(
            ['email' => 'pembeli@lokal.id'],
            [
                'nama' => 'Siti Aminah',
                'password_hash' => Hash::make('password'),
                'role' => 'pembeli',
                'no_hp' => '081298765432',
            ]
        );

        if ($pembeli->addresses()->count() === 0) {
            $pembeli->addresses()->create([
                'nama_penerima' => 'Siti Aminah',
                'no_hp' => '0812-3456-7890',
                'alamat_lengkap' => 'Jl. Merdeka No. 10',
                'kota' => 'Medan',
                'kode_pos' => '20112',
                'is_default' => true,
            ]);
        }

        // 6. User admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@lokal.id'],
            [
                'nama' => 'Admin Lokal',
                'password_hash' => Hash::make('password'),
                'role' => 'admin',
                'no_hp' => '081111111111',
            ]
        );

        $adminRecord = $admin->admin()->first();

        if ($adminRecord) {
            Category::whereNull('id_admin')->update(['id_admin' => $adminRecord->id_admin]);
            Store::whereNull('id_admin')->update(['id_admin' => $adminRecord->id_admin]);
        }
    }
}
