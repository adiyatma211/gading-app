<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Faktur Penjualan - Gading Print</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 10px;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 4px;
            text-align: center;
        }

        .no-border {
            border: none !important;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .watermark {
            position: absolute;
            top: 55%;
            /* naikkan sedikit */
            left: 45%;
            /* GESER KE KIRI */
            transform: translate(-50%, -50%) rotate(-45deg);
            z-index: -1;
            opacity: 0.08;
        }
    </style>
</head>

<body>
    @if ($transaction->status_pembayaran === 'lunas')
        <div class="watermark">
            @if(!empty($watermarkData))
                <img src="{{ $watermarkData }}" width="150">
            @endif
        </div>
    @endif

    <table>
        <tr>
            <td style="width: 60%; border: none;">
                <h2 style="margin: 0; color: red;">GADING PRINT</h2>
                <p style="margin: 0;">Digital Print Solution</p>
                <p style="margin: 5px 0;">
                    Jl. Raya Sendangmulyo No.5, Meteseh, Tembalang,<br>Kota Semarang, Jawa Tengah 50271
                </p>
                <p style="border-top: 1px solid black; border-bottom: 1px solid black;">
                    <strong>Kepada:</strong> {{ $transaction->customer->nama ?? '-' }}
                </p>
            </td>
            <td style="width: 40%; border: none; text-align: right;">
                <table style="width: 100%;">
                    <tr>
                        <td colspan="2"><strong>Faktur Penjualan</strong></td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td>{{ \Carbon\Carbon::parse($transaction->tanggal_transaksi)->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td>Nomor</td>
                        <td>{{ $transaction->nomor_faktur ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table style="margin-top: 10px;">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Keterangan</th>
                <th>Ukuran</th>
                <th>@Harga</th>
                <th>Jumlah</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaction->items as $a)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $a->produk->nama_produk }}</td>
                    <td>{{ $a->keterangan ?? '-' }}</td>
                    <td>{{ $a->panjang }} x {{ $a->lebar }}</td>
                    <td>{{ number_format($a->harga_per_meter, 0, ',', '.') }}</td>
                    <td>1</td>
                    <td>{{ number_format($a->total_harga ?? ($a->harga_per_meter * $a->panjang * $a->lebar), 0, ',', '.') }}</td>
                </tr>
                @if (($a->diskon_barang ?? 0) > 0)
                    <tr>
                        <td colspan="6" class="text-right">Diskon Item</td>
                        <td>{{ number_format($a->diskon_barang, 0, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach

            <tr>
                <td colspan="6" class="text-right">Total Barang</td>
                <td>{{ number_format($transaction->total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="6" class="text-right">Diskon</td>
                <td>{{ number_format($transaction->diskon, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="6" class="text-right">Biaya Desain</td>
                <td>{{ number_format($transaction->biaya_desain, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="6" class="text-right">DP</td>
                <td>{{ number_format($transaction->dp, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="6" class="text-right"><strong>Total Akhir</strong></td>
                <td><strong>{{ number_format($transaction->total, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>

    <p class="mt-10">
        <strong>Terbilang:</strong>
        <span id="terbilangText"
            style="border-bottom: 1px solid black; display: inline-block; width: 90%; padding-left: 10px;"></span>
    </p>

    <table style="width: 100%; margin-top: 10px;">
        <tr>
            <td style="width: 70%; vertical-align: top; text-align: left; border: none;">
                <div style="float: left; width: 60%;">
                    <p><b>Keterangan :</b></p>
                    <ul style="padding-left: 15px; margin-top: 5px; margin-bottom: 5px;">
                        <li>Komplain max. dalam 24 jam</li>
                        <li>Tunjukkan faktur ini saat ambil barang</li>
                        <li>Pembayaran Transfer ke:
                            <ul style="list-style-type: circle; margin-left: 15px;">
                                <li>BCA 8360202969 a.n HANDRI PRASODJO</li>
                                <li>Mandiri 1350017266907 a.n HANDRI PRASODJO</li>
                                <li>Bank Jateng 2034157343 a.n HANDRI PRASODJO</li>
                            </ul>
                        </li>
                    </ul>
                </div>

                <div style="float: right; width: 35%; text-align: center;">
                    <p><b>Hormat Kami,</b></p>
                    @if(!empty($logoData))
                        <img src="{{ $logoData }}" alt="Logo" style="margin-top: 20px;" height="60">
                    @endif
                </div>
            </td>
            <td class="no-border" style="width: 30%; text-align: right; padding-right: 10px;">
                <div
                    style="
            height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-end;
            margin-bottom: 120px; /* 👈 Ubah angka ini untuk sesuaikan tinggi */
        ">
                    <strong style="font-size: 10px;">TOTAL INVOICE</strong>
                    <span style="font-size: 20px; font-weight: bold;">
                        {{ number_format($transaction->total, 0, ',', '.') }}
                    </span>
                </div>
            </td>

        </tr>
    </table>

    <p style="margin-top: 10px;">
        <strong>Nota dibuat oleh:</strong> {{ Auth::user()->name ?? 'System' }}<br>
        <strong>Tanggal cetak:</strong> {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i:s') }}
    </p>

    <script>
        function terbilangJS(angka) {
            var satuan = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh",
                "Sebelas"
            ];
            angka = parseInt(angka);
            if (isNaN(angka) || angka < 0) return "Nol";
            if (angka < 12) return satuan[angka];
            if (angka < 20) return satuan[angka - 10] + " Belas";
            if (angka < 100) return terbilangJS(Math.floor(angka / 10)) + " Puluh " + terbilangJS(angka % 10);
            if (angka < 200) return "Seratus " + terbilangJS(angka - 100);
            if (angka < 1000) return terbilangJS(Math.floor(angka / 100)) + " Ratus " + terbilangJS(angka % 100);
            if (angka < 2000) return "Seribu " + terbilangJS(angka - 1000);
            if (angka < 1000000) return terbilangJS(Math.floor(angka / 1000)) + " Ribu " + terbilangJS(angka % 1000);
            if (angka < 1000000000) return terbilangJS(Math.floor(angka / 1000000)) + " Juta " + terbilangJS(angka %
                1000000);
            return "Angka terlalu besar";
        }
        let total = {{ $transaction->total ?? 0 }};
        document.getElementById('terbilangText').innerText = terbilangJS(total) + " Rupiah";
    </script>

</body>

</html>
