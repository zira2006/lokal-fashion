
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk | Pusat Penjual - Lokal Fashion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lokal-fashion.css') }}">
    <style>
        .seller-page { display: grid; grid-template-columns: 240px 1fr; min-height: 100vh; }
        .seller-sidebar { background: var(--dark); padding: 30px 0; }
        .seller-sidebar .brand { display: block; padding: 0 26px 28px; color: #f5ebe0; font-family: 'Playfair Display', serif; font-size: 19px; font-weight: 700; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,.1); margin-bottom: 20px; }
        .seller-nav { display: flex; flex-direction: column; }
        .seller-nav a { padding: 13px 26px; color: #d4c3b2; text-decoration: none; font-size: 14px; transition: all 0.2s; border-left: 3px solid transparent; }
        .seller-nav a:hover, .seller-nav a.active { color: #fff; background: rgba(255,255,255,.07); border-left-color: var(--brown); }
        .seller-main { padding: 35px 40px; }
        .seller-header h1 { font-family: 'Playfair Display', serif; font-size: 26px; color: var(--dark); margin: 0 0 28px; }

        .form-card { background: var(--white); border: 1px solid var(--line); border-radius: 14px; padding: 28px; margin-bottom: 22px; }
        .form-card h2 { font-family: 'Playfair Display', serif; font-size: 18px; color: var(--dark); margin: 0 0 20px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: var(--dark); margin-bottom: 7px; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px 15px; border: 1px solid var(--line); border-radius: 10px; font-size: 14px; font-family: inherit; background: #fff; outline: none; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: var(--brown); }
        .form-error { color: #a8442e; font-size: 12px; margin-top: 5px; }
        .form-hint { font-size: 11.5px; color: var(--muted); margin: 7px 0 0; line-height: 1.5; }
        .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; }

        .file-picker { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .form-group .file-input { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; background: transparent; }
        .file-input:focus + label { outline: 2px solid var(--brown); outline-offset: 2px; }
        .file-name { font-size: 12px; color: var(--muted); }
        .image-preview { margin-top: 14px; width: 150px; height: 200px; border: 1.5px dashed var(--line); border-radius: 12px; background: var(--cream); display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .image-preview.has-image { border-style: solid; }
        .image-preview img { width: 100%; height: 100%; object-fit: cover; display: none; }
        .image-preview-empty { font-size: 11px; color: var(--muted); text-align: center; padding: 10px; }

        .variant-image-list { display: flex; flex-direction: column; }
        .variant-image-row { display: flex; align-items: center; gap: 16px; padding: 14px 0; border-bottom: 1px solid var(--line); }
        .variant-image-row:last-child { border-bottom: none; padding-bottom: 0; }
        .variant-image-current { width: 54px; height: 68px; border: 1px solid var(--line); border-radius: 8px; background: var(--cream); overflow: hidden; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
        .variant-image-current img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .variant-image-empty { font-size: 9px; color: var(--muted); text-align: center; padding: 4px; }
        .variant-image-detail { flex: 1; min-width: 0; }
        .variant-image-name { font-size: 13px; font-weight: 600; color: var(--dark); margin-bottom: 8px; }
        .variant-image-name span { font-weight: 400; color: var(--muted); }
        .variant-image-picker { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .variant-image-picker .file-input { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; background: transparent; }
        .variant-image-picker .file-input:focus + label { outline: 2px solid var(--brown); outline-offset: 2px; }

        .btn { display: inline-block; padding: 13px 28px; border: none; border-radius: 25px; font-size: 14px; font-weight: 600; font-family: inherit; cursor: pointer; text-decoration: none; text-align: center; transition: all 0.2s; }
        .btn-primary { background: var(--dark); color: #fff; }
        .btn-primary:hover { background: #6d5340; }
        .btn-outline { background: transparent; border: 1.5px solid var(--dark); color: var(--dark); }
        .btn-sm { padding: 9px 18px; font-size: 12px; }

        @media (max-width: 800px) {
            .seller-page { grid-template-columns: 1fr; }
            .seller-sidebar { padding: 15px 0; }
            .seller-nav { flex-direction: row; overflow-x: auto; }
            .seller-nav a { white-space: nowrap; border-left: none; border-bottom: 3px solid transparent; }
            .seller-nav a:hover, .seller-nav a.active { border-bottom-color: var(--brown); }
            .seller-main { padding: 25px 18px; }
        }
    </style>
</head>
<body>
    <div class="seller-page">
                @include('layout.partials.dashboard-sidebar')

        <main class="seller-main">
            <div class="seller-header">
                <h1>Edit Produk</h1>
            </div>

            <form method="POST" action="{{ route('seller.products.update', $product->id_produk) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-card">
                    <h2>Informasi Produk</h2>
                    <div class="form-group">
                        <label for="nama_produk">Nama Produk</label>
                        <input type="text" id="nama_produk" name="nama_produk" value="{{ old('nama_produk', $product->nama_produk) }}" required>
                        @error('nama_produk') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="id_kategori">Kategori</label>
                            <select id="id_kategori" name="id_kategori" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id_kategori }}" {{ old('id_kategori', $product->id_kategori) == $cat->id_kategori ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                                @endforeach
                            </select>
                            @error('id_kategori') <div class="form-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label for="kota_asal">Kota Asal Produk</label>
                            <input type="text" id="kota_asal" name="kota_asal" value="{{ old('kota_asal', $product->kota_asal ?: $store->kota_asal) }}" placeholder="Contoh: Banda Aceh" required>
                            <p class="form-hint">Kota asal akan tampil di halaman produk pembeli.</p>
                            @error('kota_asal') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" rows="4">{{ old('deskripsi', $product->deskripsi) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="harga_dasar">Harga Dasar (Rp)</label>
                        <input type="text" id="harga_dasar" name="harga_dasar" value="{{ old('harga_dasar', number_format((float) $product->harga_dasar, 0, ',', '.')) }}" class="rupiah-input" inputmode="numeric" autocomplete="off" placeholder="Contoh: 200.000" required>
                        <p class="form-hint">Gunakan format ribuan, contoh: 200.000.</p>
                        @error('harga_dasar') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="gambar">Gambar Utama Produk</label>
                        <div class="file-picker">
                            <input type="file" id="gambar" name="gambar" accept="image/jpeg,image/png,image/webp" class="file-input"
                                   onchange="previewImage(this, 'gambarPreview', 'gambarFileName')">
                            <label for="gambar" class="btn btn-outline btn-sm">Pilih dari Galeri</label>
                            <span class="file-name" id="gambarFileName">{{ $product->gambar ? 'Gambar saat ini tersimpan' : 'Belum ada gambar dipilih' }}</span>
                        </div>
                        <p class="form-hint">Gambar utama dipakai di katalog. Kosongkan jika tidak ingin mengganti. Format JPG, PNG, atau WEBP, maks. 2 MB.</p>
                        <div class="image-preview {{ $product->gambar ? 'has-image' : '' }}" id="gambarPreview">
                            <img src="{{ $product->gambar ? $product->gambar_url : '' }}" alt="Gambar produk"
                                 style="{{ $product->gambar ? 'display:block;' : 'display:none;' }}"
                                 onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}';">
                            <span class="image-preview-empty" style="{{ $product->gambar ? 'display:none;' : '' }}">Pratinjau gambar</span>
                        </div>
                        @error('gambar') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                @if($product->variants->isNotEmpty())
                    <div class="form-card">
                        <h2>Gambar Varian per Warna</h2>
                        <p class="form-hint" style="margin: -12px 0 18px;">Ganti gambar khusus untuk warna tertentu. Kosongkan jika tidak ingin mengubah gambar warna tersebut.</p>

                        <div class="variant-image-list">
                            @foreach($product->variants as $index => $variant)
                                <div class="variant-image-row">
                                    <div class="variant-image-current">
                                        @if($variant->gambar)
                                            <img src="{{ $variant->gambar_url }}" alt="Gambar {{ $variant->warna }}"
                                                 onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}';">
                                        @else
                                            <span class="variant-image-empty">Belum ada</span>
                                        @endif
                                    </div>
                                    <div class="variant-image-detail">
                                        <div class="variant-image-name">{{ $variant->warna }} <span>· {{ $variant->ukuran }}</span></div>
                                        <div class="variant-image-picker">
                                            <input type="hidden" name="variants[{{ $index }}][id_varian]" value="{{ $variant->id_varian }}">
                                            <input type="file" id="variantImage{{ $index }}" name="variants[{{ $index }}][gambar]" accept="image/jpeg,image/png,image/webp" class="file-input"
                                                   onchange="showVariantFileName(this, 'variantFileName{{ $index }}')">
                                            <label for="variantImage{{ $index }}" class="btn btn-outline btn-sm">Pilih dari Galeri</label>
                                            <span class="file-name" id="variantFileName{{ $index }}">Tidak diubah</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($errors->has('variants.*.gambar'))
                            <div class="form-error">{{ $errors->first('variants.*.gambar') }}</div>
                        @endif
                    </div>
                @endif

                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </main>
    </div>

    <script>
        function showVariantFileName(input, fileNameId) {
            const fileName = document.getElementById(fileNameId);
            if (!fileName) return;
            fileName.textContent = input.files && input.files[0] ? input.files[0].name : 'Tidak diubah';
        }

        function previewImage(input, previewId, fileNameId) {
            const preview = document.getElementById(previewId);
            const image = preview.querySelector('img');
            const empty = preview.querySelector('.image-preview-empty');
            const fileName = document.getElementById(fileNameId);

            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();

                reader.onload = function (event) {
                    image.src = event.target.result;
                    image.style.display = 'block';
                    if (empty) empty.style.display = 'none';
                    preview.classList.add('has-image');
                };

                reader.readAsDataURL(file);
                fileName.textContent = file.name;
            } else {
                image.src = '';
                image.style.display = 'none';
                if (empty) empty.style.display = 'block';
                preview.classList.remove('has-image');
                fileName.textContent = 'Belum ada gambar dipilih';
            }
        }
    </script>
    <script src="{{ asset('js/rupiah-input.js') }}"></script>
</body>
</html>
