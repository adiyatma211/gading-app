<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GADING PRINT Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;

            color: black;
            font-size: 12px;
        }

        .receipt {
            max-width: 300px;
            margin: auto;
            padding: 10px;

            color: black;
        }

        .receipt header {
            text-align: center;
        }

        .receipt .store-logo {
            text-align: center;
        }

        .store-logo img {
            max-width: 100px;
            margin-bottom: 10px;
        }

        .receipt .store-info {
            margin-bottom: 5px;
            line-height: 1.2;
        }

        .receipt hr {
            border: 1px dashed #000000;
        }

        .receipt .items {
            margin-top: 5px;
            line-height: 1.2;
        }

        .receipt .item {
            display: flex;
            justify-content: space-between;
            line-height: 1.2;
        }

        .receipt .total {
            font-weight: bold;
        }

        .receipt .footer {
            text-align: center;
            margin-top: 15px;
        }

        .receipt .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="receipt">
        <header>
            <div class="store-logo">
                <img src="{{ $logoPath }}" alt="GADING PRINT Logo" />
            </div>
            <h2>GADING PRINT</h2>
            <p>Digital Print Solution</p>
            <p>Jl. Raya Sendangmulyo No.5, Meteseh, Tembalang</p>
            <p>Kota Semarang, Jawa Tengah 50271</p>
        </header>
        <hr />
        <div class="header-info">
            <p>
                Tanggal:
                {{ \Carbon\Carbon::parse($transaction->tanggal_transaksi)->translatedFormat('d
                                                                                                                                                                                                                                                                                                                                                                                          F Y') }}
            </p>
            <p>Nomor: {{ $transaction->nomor_faktur ?? '-' }}</p>
            <p>Customer: {{ $transaction->customer->nama ?? '-' }}</p>
        </div>
        <hr />
        <div class="items">
            @foreach ($transaction->items as $a)
                <div class="item">
                    <strong>{{ $a->produkBahan->nama_bahan }}</strong>
                    <span>Rp
                        {{ number_format($a->harga_per_meter * $a->panjang * $a->lebar, 0, ',', '.') }}</span>
                </div>
                <div class="item">
                    <small>{{ $a->panjang }} x {{ $a->lebar }} m @ Rp
                        {{ number_format($a->harga_per_meter, 0, ',', '.') }}</small>
                </div>
            @endforeach
        </div>

        <hr />
        <div class="total">
            <p class="text-right">
                Sub Total: Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}
            </p>
            <p class="text-right">
                Total: Rp {{ number_format($transaction->total, 0, ',', '.') }}
            </p>
            <p class="text-right">
                Bayar (Cash): Rp {{ number_format($transaction->total, 0, ',', '.') }}
            </p>
            <p class="text-right">
                Kembali: Rp {{ number_format($transaction->kembalian, 0, ',', '.') }}
            </p>
        </div>
        <div class="footer">
            <p class="text-center"><b>Terima Kasih</b></p>
            <p class="text-center">Komplain max. dalam 24 jam</p>
            <p class="text-center">Pengambilan barang mohon menunjukan nota ini</p>
        </div>
    </div>
</body>

</html>
