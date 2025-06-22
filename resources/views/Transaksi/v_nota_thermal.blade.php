=== NOTA TRANSAKSI ===

Nama : {{ $transaction->customer->nama }}
Faktur : {{ $transaction->nomor_faktur }}
Tgl : {{ now()->format('d-m-Y H:i') }}

--------------------------
@foreach ($transaction->items as $item)
    {{ $item->keterangan }}
    {{ $item->panjang }} x {{ $item->lebar }} @Rp{{ number_format($item->harga_per_meter) }}/m
@endforeach
--------------------------
Subtotal : Rp{{ number_format($transaction->subtotal) }}
Diskon : Rp{{ number_format($transaction->diskon) }}
Desain : Rp{{ number_format($transaction->biaya_desain) }}
DP : Rp{{ number_format($transaction->dp) }}
TOTAL : Rp{{ number_format($transaction->total) }}
Bayar : {{ $transaction->status_pembayaran }}
