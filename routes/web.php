<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SellerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'home'])->name('home');
Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/produk/{slug}', [ProductController::class, 'show'])->name('product.detail');

// Auth — satu halaman login untuk pembeli, penjual, dan admin
Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/masuk', [AuthController::class, 'login'])->middleware('guest');
Route::get('/masuk/penjual', [AuthController::class, 'showSellerLogin'])->name('seller.login')->middleware('guest');
Route::get('/masuk/admin', [AuthController::class, 'showAdminLogin'])->name('admin.login')->middleware('guest');
Route::post('/daftar', [AuthController::class, 'register'])->name('register')->middleware('guest');
// Logout tidak memakai middleware auth agar tetap aman dipanggil saat sesi
// sudah kedaluwarsa atau halaman lama masih terbuka di browser.
Route::post('/keluar', [AuthController::class, 'logout'])->name('logout');

// Dashboard bersama untuk penjual dan admin
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth', 'role:penjual,admin')
    ->name('dashboard');

// Cart
Route::prefix('keranjang')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/tambah', [CartController::class, 'add'])->name('add');
    Route::post('/perbarui', [CartController::class, 'update'])->name('update');
    Route::post('/hapus', [CartController::class, 'remove'])->name('remove');
    Route::post('/kosongkan', [CartController::class, 'clear'])->name('clear');
    Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout');
});

// Beli langsung dari halaman produk, tanpa memasukkan produk ke keranjang
Route::post('/beli-sekarang', [CheckoutController::class, 'buyNow'])->name('checkout.buyNow');
Route::post('/beli-sekarang/batal', [CheckoutController::class, 'cancelBuyNow'])->name('checkout.buyNow.cancel');

// Checkout, Payment, Order (harus login)
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('/checkout/alamat', [CheckoutController::class, 'storeAddress'])->name('checkout.address');
    Route::get('/pembayaran/{invoice}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/pembayaran/{invoice}', [PaymentController::class, 'pay'])->name('payment.pay');
    Route::get('/pembayaran/{invoice}/hubungkan', [PaymentController::class, 'connectForm'])->name('payment.connect.form');
    Route::post('/pembayaran/{invoice}/hubungkan', [PaymentController::class, 'storeConnection'])->name('payment.connect.store');
    Route::get('/pesanan-saya', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan-saya/{invoice}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/pesanan-saya/{invoice}/terima', [OrderController::class, 'receive'])->name('orders.receive');
    Route::post('/pesanan-saya/{invoice}/ulasan', [OrderController::class, 'storeReview'])->name('orders.review');
});

// Alias agar URL /akun/profil tetap mengarah ke profil pembeli
Route::redirect('/akun', '/profil');
Route::redirect('/akun/profil', '/profil');

// Profile (harus login)
Route::prefix('profil')->name('profile.')->middleware('auth')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
    Route::post('/foto', [ProfileController::class, 'updatePhoto'])->name('photo');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
    Route::get('/alamat', [ProfileController::class, 'addresses'])->name('addresses');
    Route::post('/alamat', [ProfileController::class, 'storeAddress'])->name('addresses.store');
    Route::get('/alamat/{id}/edit', [ProfileController::class, 'editAddress'])->name('addresses.edit');
    Route::put('/alamat/{id}', [ProfileController::class, 'updateAddress'])->name('addresses.update');
    Route::delete('/alamat/{id}', [ProfileController::class, 'deleteAddress'])->name('addresses.delete');
    Route::post('/alamat/{id}/utama', [ProfileController::class, 'setDefaultAddress'])->name('addresses.default');
});

// Seller (harus login sebagai penjual)
Route::prefix('penjual')->name('seller.')->middleware('auth', 'role:penjual')->group(function () {
    Route::get('/profil', [SellerController::class, 'profile'])->name('profile');
    Route::get('/profil/edit', [SellerController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profil', [SellerController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profil/foto', [SellerController::class, 'updateProfilePhoto'])->name('profile.photo');
    Route::get('/produk', [SellerController::class, 'products'])->name('products');
    Route::get('/produk/baru', [SellerController::class, 'createProduct'])->name('products.create');
    Route::post('/produk', [SellerController::class, 'storeProduct'])->name('products.store');
    Route::get('/produk/{id}/edit', [SellerController::class, 'editProduct'])->name('products.edit');
    Route::put('/produk/{id}', [SellerController::class, 'updateProduct'])->name('products.update');
    Route::delete('/produk/{id}', [SellerController::class, 'destroyProduct'])->name('products.destroy');
    Route::post('/produk/{id}/status', [SellerController::class, 'toggleProductStatus'])->name('products.toggle');
    Route::get('/pesanan', [SellerController::class, 'orders'])->name('orders');
    Route::post('/pesanan/{invoice}', [SellerController::class, 'updateOrderStatus'])->name('orders.update');
});

// Admin (harus login sebagai admin)
Route::prefix('admin')->name('admin.')->middleware('auth', 'role:admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/pengguna', [AdminController::class, 'users'])->name('users');
    Route::get('/toko', [AdminController::class, 'sellers'])->name('sellers');
    Route::post('/toko/{id}/verifikasi', [AdminController::class, 'verifyStore'])->name('sellers.verify');
    Route::post('/toko/{id}/tolak', [AdminController::class, 'rejectStore'])->name('sellers.reject');
    Route::get('/pesanan', [AdminController::class, 'orders'])->name('orders');
});
