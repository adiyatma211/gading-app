<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Nota Transaksi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 5px;
            color: #000;
            margin: 0;
            width: 72mm;
            padding: 0;
        }


        @page {
            margin: 2mm;
        }

        .nota {
            /* background: red; */
            width: 55mm;
            margin-right: 16mm;
            padding: 0;
        }


        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        hr.dashed {
            border: none;
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            font-size: 6px;
            word-break: break-word;
            /* pastikan kata tidak nabrak */
            flex-wrap: wrap;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
        }

        img.logo {
            max-width: 30mm;
            /* aman untuk 72mm thermal printer */
            height: auto;
            margin-bottom: 2mm;
        }
    </style>
</head>

<body>
    <div class="nota">
        <div class="text-center">
            <img src="{{ public_path('assets/logoSVG.svg') }}" alt="Gading Print" class="logo">
            <h3 class="bold">GADING PRINT</h3>
            <p>Digital Print Solution</p>
            <p>Jl. Raya Sendangmulyo No.5, Meteseh, Tembalang</p>
            <p>Kota Semarang, Jawa Tengah 50271</p>
        </div>

        <hr class="dashed">

        <p><strong>Tanggal:</strong>
            {{ \Carbon\Carbon::parse($transaction->tanggal_transaksi)->translatedFormat('d F Y') }}</p>
        <p><strong>Nomor:</strong> {{ $transaction->nomor_faktur }}</p>
        <p><strong>Customer:</strong> {{ $transaction->customer->nama ?? '-' }}</p>

        <hr class="dashed">

        @foreach ($transaction->items as $item)
            <div class="item-row">
                <span>
                    {{ $item->produk?->nama_produk ?? 'Produk #' . $item->tipe_produk_id }}
                    @if ($item->panjang && $item->lebar)
                        ({{ number_format($item->panjang, 2) }}m x {{ number_format($item->lebar, 2) }}m)
                    @elseif ($item->qty)
                        (Qty: {{ $item->qty }})
                    @endif
                </span>

                <span>
                    Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                </span>
            </div>

            <small>
                @if ($item->panjang && $item->lebar)
                    {{ number_format($item->panjang, 2) }} x {{ number_format($item->lebar, 2) }} @
                    Rp {{ number_format($item->harga_per_meter, 0, ',', '.') }}
                @elseif ($item->qty)
                    {{ $item->qty }} x Rp {{ number_format($item->harga_per_meter, 0, ',', '.') }}
                @else
                    Rp {{ number_format($item->harga_per_meter, 0, ',', '.') }}
                @endif

                @if ($item->keterangan)
                    (Ket: {{ $item->keterangan }})
                @endif
            </small>
            <hr class="dashed">
        @endforeach


        <div class="text-right">
            <p><strong>Metode Pembayaran:</strong> {{ strtoupper($transaction->metode_pembayaran) }}</p>
            <p><strong>Sub Total:</strong> Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</p>
            <p><strong>Total:</strong> Rp {{ number_format($transaction->total, 0, ',', '.') }}</p>
            @if ($transaction->metode_pembayaran === 'tunai')
                <p><strong>Bayar (Cash):</strong> Rp {{ number_format($transaction->total, 0, ',', '.') }}</p>
            @else
                <p><strong>Bayar ({{ \Illuminate\Support\Str::title($transaction->metode_pembayaran) }}):</strong> Rp
                    {{ number_format($transaction->total, 0, ',', '.') }}</p>
            @endif
            {{-- <p><strong>Kembali:</strong> Rp {{ number_format($transaction->total - $transaction->dp, 0, ',', '.') }}
            </p> --}}
        </div>

        <div class="footer">
            <p class="bold">Terima Kasih</p>
            <p>Komplain max. dalam 24 jam</p>
            <p>Pengambilan barang mohon menunjukan nota ini</p>
        </div>
    </div>
</body>

</html>
