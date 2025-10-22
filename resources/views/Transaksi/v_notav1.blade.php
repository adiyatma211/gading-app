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
            /* Center the 72mm content in print preview/browser */
            margin: 0 auto;
            width: {{ $thermalWidth ?? 72 }}mm;
            padding: 0;
        }


        @page {
            margin: 2mm;
        }

        .nota {
            /* background: red; */
            width: 55mm;
            /* Center the inner receipt block */
            margin: 0 auto;
            padding: 0;
        }

        @media print {
            html, body {
                width: {{ $thermalWidth ?? 72 }}mm;
                margin: 0 auto; /* ensure centering on A4/Letter previews */
            }
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
            @if(!empty($logoData))
                <img src="{{ $logoData }}" alt="Gading Print" class="logo">
            @else
                <img src="{{ asset('assets/logoSVG.svg') }}" alt="Gading Print" class="logo">
            @endif
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


        @php
            $subtotalCalc = $transaction->items->sum('total_harga');
            $diskon = (float) ($transaction->diskon ?? 0);
            $biayaDesain = (float) ($transaction->biaya_desain ?? 0);
            $dp = (float) ($transaction->dp ?? 0);
            $grandTotal = max(0, $subtotalCalc + $biayaDesain - $diskon);
            $sisa = max(0, $grandTotal - $dp);
        @endphp
        <div class="text-right">
            <p><strong>Metode Pembayaran:</strong> {{ strtoupper($transaction->metode_pembayaran) }}</p>
            <p><strong>Sub Total:</strong> Rp {{ number_format($subtotalCalc, 0, ',', '.') }}</p>
            @if($diskon > 0)
                <p><strong>Diskon:</strong> Rp {{ number_format($diskon, 0, ',', '.') }}</p>
            @endif
            @if($biayaDesain > 0)
                <p><strong>Biaya Desain:</strong> Rp {{ number_format($biayaDesain, 0, ',', '.') }}</p>
            @endif
            @if($dp > 0)
                <p><strong>DP:</strong> Rp {{ number_format($dp, 0, ',', '.') }}</p>
            @endif
            <p><strong>Total:</strong> Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
            @if($dp > 0)
                <p><strong>Sisa:</strong> Rp {{ number_format($sisa, 0, ',', '.') }}</p>
            @endif
        </div>

        <div class="footer">
            <p class="bold">Terima Kasih</p>
            <p>Komplain max. dalam 24 jam</p>
            <p>Pengambilan barang mohon menunjukan nota ini</p>
        </div>
    </div>
</body>

</html>
