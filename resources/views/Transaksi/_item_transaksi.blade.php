<div class="mmt-item row g-3">
    <span class="badge">Produk {{ $index + 1 }}</span>
    <div class="col-md-3">
        <label>Tipe Produk</label>
        <select class="form-select tipe-produk" name="items[{{ $index }}][tipe]" required>
            <option value="">-- Pilih Produk --</option>
            @foreach ($showProdak as $produk)
                <option value="{{ $produk->id }}" data-harga="{{ optional($produk->hargas->first())->harga ?? 0 }}"
                    data-diskon="{{ optional($produk->hargas->first())->diskon ?? 0 }}"
                    data-tipe-produk="{{ $produk->tipe_produk }}">
                    {{ $produk->nama_produk }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2 panjang-lebar" style="display: none;">
        <label>Panjang (m)</label>
        <input type="number" step="0.1" name="items[{{ $index }}][panjang]">
    </div>
    <div class="col-md-2 panjang-lebar" style="display: none;">
        <label>Lebar (m)</label>
        <input type="number" step="0.1" name="items[{{ $index }}][lebar]">
    </div>
    <div class="col-md-2 qty" style="display: none;">
        <label>Jumlah (Qty)</label>
        <input type="number" name="items[{{ $index }}][qty]">
    </div>
    <div class="col-md-2 custom-inputs" style="display: none;">
        <label>Sisi</label>
        <select name="items[{{ $index }}][sisi]">
            <option value="1">1 Sisi</option>
            <option value="2">2 Sisi</option>
        </select>
    </div>
    <div class="col-md-2 custom-inputs" style="display: none;">
        <label>Laminasi</label>
        <select name="items[{{ $index }}][laminasi]">
            <option value="tidak">Tidak</option>
            <option value="ya">Ya</option>
        </select>
    </div>
    <div class="col-md-2">
        <label>Harga Satuan</label>
        <input type="text" class="rupiah-input" name="items[{{ $index }}][harga]" readonly>
    </div>
    <div class="col-md-2">
        <label>Diskon Barang</label>
        <input type="text" class="rupiah-input" name="items[{{ $index }}][diskonbarang]" disabled>
    </div>
    <div class="col-md-3">
        <label>Keterangan</label>
        <textarea name="items[{{ $index }}][keterangan]"></textarea>
    </div>
    <div class="col-md-2">
        <button type="button" class="btn btn-danger btn-remove-item">Hapus</button>
    </div>
</div>
