
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk | Pusat Penjual - Lokal Fashion</title>
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

        .btn { display: inline-block; padding: 13px 28px; border: none; border-radius: 25px; font-size: 14px; font-weight: 600; font-family: inherit; cursor: pointer; text-decoration: none; text-align: center; transition: all 0.2s; }
        .btn-primary { background: var(--dark); color: #fff; }
        .btn-primary:hover { background: #6d5340; }
        .btn-outline { background: transparent; border: 1.5px solid var(--dark); color: var(--dark); }
        .btn-sm { padding: 9px 18px; font-size: 12px; }

        .variant-table-wrap { overflow-x: auto; }
        .variant-table { width: 100%; border-collapse: collapse; }
        .variant-table th { background: var(--beige); padding: 11px 14px; text-align: left; font-size: 11px; font-weight: 700; color: var(--dark); text-transform: uppercase; }
        .variant-table td { padding: 8px 10px; border-top: 1px solid var(--line); }
        .variant-table input { width: 100%; padding: 10px 12px; border: 1px solid var(--line); border-radius: 8px; font-size: 13px; font-family: inherit; }
        .variant-table input[type="file"] { padding: 7px 8px; font-size: 11px; border: 1px dashed var(--soft-brown); background: var(--cream); cursor: pointer; }
        .variant-table input[type="file"]::file-selector-button { margin-right: 6px; padding: 5px 9px; border: none; border-radius: 14px; background: var(--dark); color: #fff; font-size: 10px; font-family: inherit; cursor: pointer; }
        .variant-table .btn-remove { background: none; border: none; color: #a8442e; cursor: pointer; font-size: 16px; }

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
                <h1>Tambah Produk & Varian</h1>
            </div>

            <form method="POST" action="{{ route('seller.products.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-card">
                    <h2>Informasi Produk</h2>
                    <div class="form-group">
                        <label for="nama_produk">Nama Produk</label>
                        <input type="text" id="nama_produk" name="nama_produk" value="{{ old('nama_produk') }}" required>
                        @error('nama_produk') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="id_kategori">Kategori</label>
                            <select id="id_kategori" name="id_kategori" required>
                                <option value="">Pilih kategori...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id_kategori }}" {{ old('id_kategori') == $cat->id_kategori ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                                @endforeach
                            </select>
                            @error('id_kategori') <div class="form-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label for="kota_asal">Kota Asal Produk</label>
                            <input type="text" id="kota_asal" name="kota_asal" value="{{ old('kota_asal', $store->kota_asal) }}" placeholder="Contoh: Banda Aceh" required>
                            <p class="form-hint">Kota asal akan tampil di halaman produk pembeli.</p>
                            @error('kota_asal') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" rows="4">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="harga_dasar">Harga Dasar (Rp)</label>
                        <input type="text" id="harga_dasar" name="harga_dasar" value="{{ old('harga_dasar') }}" class="rupiah-input" inputmode="numeric" autocomplete="off" placeholder="Contoh: 200.000" required>
                        <p class="form-hint">Gunakan format ribuan, contoh: 200.000.</p>
                        @error('harga_dasar') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label for="gambar">Gambar Utama Produk</label>
                        <div class="file-picker">
                            <input type="file" id="gambar" name="gambar" accept="image/jpeg,image/png,image/webp" class="file-input"
                                   onchange="previewImage(this, 'gambarPreview', 'gambarFileName')">
                            <label for="gambar" class="btn btn-outline btn-sm">Pilih dari Galeri</label>
                            <span class="file-name" id="gambarFileName">Belum ada gambar dipilih</span>
                        </div>
                        <p class="form-hint">Opsional. Jika kosong, gambar varian pertama dipakai sebagai gambar katalog. Format JPG, PNG, atau WEBP, maks. 2 MB.</p>
                        <div class="image-preview" id="gambarPreview">
                            <img alt="Pratinjau gambar produk">
                            <span class="image-preview-empty">Pratinjau gambar</span>
                        </div>
                        @error('gambar') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-card">
                    <h2>Varian Produk</h2>
                    <p class="form-hint" style="margin: -12px 0 16px;">Kota asal yang diisi di atas berlaku untuk semua varian. Unggah gambar dari galeri untuk tiap warna; gambar akan tampil saat pembeli memilih warna tersebut.</p>
                    <div class="variant-table-wrap">
                        <table class="variant-table" id="variantTable">
                            <thead>
                                <tr>
                                    <th>Warna</th>
                                    <th>Ukuran</th>
                                    <th>Stok</th>
                                    <th>Harga</th>
                                    <th>Gambar Warna</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" name="variants[0][warna]" required></td>
                                    <td><input type="text" name="variants[0][ukuran]" required></td>
                                    <td><input type="number" name="variants[0][stok]" min="0" required></td>
                                    <td><input type="text" name="variants[0][harga]" class="rupiah-input" inputmode="numeric" autocomplete="off" placeholder="200.000" required></td>
                                    <td><input type="file" name="variants[0][gambar]" accept="image/jpeg,image/png,image/webp"></td>
                                    <td><button type="button" class="btn-remove" onclick="removeVariantRow(this)" title="Hapus">×</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline btn-sm" onclick="addVariantRow()" style="margin-top: 14px;">+ Tambah Varian</button>
                    @error('variants') <div class="form-error">{{ $message }}</div> @enderror
                    @if($errors->has('variants.*.gambar'))
                        <div class="form-error">{{ $errors->first('variants.*.gambar') }}</div>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary">Simpan Produk</button>
            </form>
        </main>
    </div>

    <script>
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

        let variantIndex = 1;
        function addVariantRow() {
            const tbody = document.querySelector('#variantTable tbody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" name="variants[${variantIndex}][warna]" required></td>
                <td><input type="text" name="variants[${variantIndex}][ukuran]" required></td>
                <td><input type="number" name="variants[${variantIndex}][stok]" min="0" required></td>
                <td><input type="text" name="variants[${variantIndex}][harga]" class="rupiah-input" inputmode="numeric" autocomplete="off" placeholder="200.000" required></td>
                <td><input type="file" name="variants[${variantIndex}][gambar]" accept="image/jpeg,image/png,image/webp"></td>
                <td><button type="button" class="btn-remove" onclick="removeVariantRow(this)" title="Hapus">×</button></td>
            `;
            tbody.appendChild(row);
            variantIndex++;
        }
        function removeVariantRow(btn) {
            btn.closest('tr').remove();
        }
    </script>
    <script src="{{ asset('js/rupiah-input.js') }}"></script>
</body>
</html>
