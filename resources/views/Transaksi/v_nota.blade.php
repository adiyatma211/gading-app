<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Faktur Penjualan - Gading Print</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            position: relative;
            /* Untuk watermark */
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
        }

        .no-border {
            border: none;
        }

        .text-left {
            text-align: left;
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

        .underline {
            text-decoration: underline;
        }

        .red {
            color: red;
        }

        .blue {
            color: #003366;
        }

        .dashed-border {
            border: 2px dashed black;
            padding: 5px;
        }

        .invoice-total {
            border: 2px solid black;
            padding: 5px;
            font-size: 16px;
        }

        ul {
            list-style-type: disc;
            margin-left: 20px;
        }

        .mt-10 {
            margin-top: 10px;
        }

        .mt-20 {
            margin-top: 20px;
        }

        /* WATERMARK */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            z-index: -1;
            /* Pastikan watermark berada di belakang konten */
            transform: translate(-50%, -50%) rotate(-45deg);
            /* Posisi tengah dan rotasi */
            opacity: 0.3;
            /* Transparansi */
            font-size: 100px;
            font-weight: bold;
            color: #ccc;
            pointer-events: none;
            /* Agar watermark tidak mengganggu interaksi */
        }
    </style>
</head>

<body>


    <!-- WATERMARK -->
    @if ($transaction->status_pembayaran === 'lunas')
        <div class="watermark">
            <img src="{{ $watermarkPath }}" alt="Watermark Lunas" style="width: 300px; height: auto;">
        </div>
    @endif

    <!-- HEADER -->
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <!-- Kiri: Logo dan Customer -->
            <td style="width: 60%; vertical-align: top; border: none;">
                <div class="text-left" style="margin-left: 10px;">
                    <h1 class="red" style="margin:0;">GADING PRINT</h1>
                    <p style="margin:0;"><b>Digital Print Solution</b></p>
                    <br>
                    <p style="margin:0;">
                        Jl. Raya Sendangmulyo No.5, Meteseh, Kec. Tembalang,<br>
                        Kota Semarang, Jawa Tengah 50271
                    </p>
                    <p
                        style="border-top: 2px solid black; border-bottom: 2px solid black; width: 70%; padding: 5px; margin-top: 20px;">
                        <b>Kepada :</b> <span
                            style="font-weight: bold; margin-left: 10px;">{{ $transaction->customer->nama ?? '-' }}</span>
                    </p>
                </div>
            </td>

            <!-- Kanan: Faktur Penjualan -->
            <td style="width: 40%; vertical-align: top; border: none;">
                <div class="text-right" style="margin-right: 10px; margin-top: 45px;">
                    <table style="width: 100%; text-align: center; border-collapse: collapse;">
                        <tr>
                            <td colspan="2" style="font-weight: bold; border: none;">Faktur Penjualan</td>
                        </tr>
                        <tr>
                            <td style="border: 1px dashed black;"><b>Tanggal</b></td>
                            <td style="border: 1px dashed black;">
                                {{ \Carbon\Carbon::parse($transaction->tanggal_transaksi)->translatedFormat('d F Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 1px dashed black;"><b>Nomor</b></td>
                            <td style="border: 1px dashed black;">{{ $transaction->nomor_faktur ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- SPASI -->
    <div style="margin-top: 15px;"></div>

    <!-- TABEL BARANG -->
    <table style="width: 98%; border-collapse: collapse; margin-top: 10px; margin-left: 10px;">
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
                    <td>{{ $a->produkBahan->nama_bahan }}</td>
                    <td>{{ $a->keterangan ?? '-' }}</td>
                    <td style="padding: 0;">
                        <table style="width: 100%; border-collapse: collapse; text-align: center;">
                            <tr>
                                <!-- Kolom pertama (Panjang) -->
                                <td style="border: 1px solid black; width: 33%; padding: 5px;">{{ $a->panjang }}</td>

                                <!-- Kolom kedua (Operator "*") -->
                                <td style="border: 1px solid black; width: 10%; padding: 5px;">*</td>

                                <!-- Kolom ketiga (Lebar) -->
                                <td style="border: 1px solid black; width: 33%; padding: 5px;">{{ $a->lebar }}</td>
                            </tr>
                        </table>
                    </td>
                    <td>{{ number_format($a->harga_per_meter, 0, ',', '.') }}</td>
                    <td>1</td>
                    <td>{{ number_format($a->harga_per_meter * $a->panjang * $a->lebar, 0, ',', '.') }}</td>
                </tr>
            @endforeach

            <!-- Total Barang -->
            <tr>
                <td colspan="6" style="text-align: right; font-weight: bold;">Total Barang</td>
                <td style="font-weight: bold;">{{ number_format($transaction->total, 0, ',', '.') }}</td>
            </tr>

            <!-- Diskon -->
            <tr>
                <td colspan="6" style="text-align: right;">Diskon</td>
                <td>{{ number_format($transaction->diskon, 0, ',', '.') }}</td>
            </tr>

            <!-- Biaya Desain -->
            <tr>
                <td colspan="6" style="text-align: right;">Biaya Desain</td>
                <td>{{ number_format($transaction->biaya_desain, 0, ',', '.') }}</td>
            </tr>

            <!-- DP (Down Payment) -->
            <tr>
                <td colspan="6" style="text-align: right;">DP (Down Payment)</td>
                <td>{{ number_format($transaction->dp, 0, ',', '.') }}</td>
            </tr>

            <!-- Total Akhir -->
            <tr>
                <td colspan="6" style="text-align: right; font-weight: bold;">Total Akhir</td>
                <td style="font-weight: bold;">{{ number_format($transaction->total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- TERBILANG -->
    <p class="mt-20" style="margin-top: 20px; margin-left: 10px;">
        <b>Terbilang :</b>
        <span id="terbilangText"
            style="display: inline-block; border-bottom: 2px solid black; font-weight: bold; width: 95%; padding-bottom: 3px; margin-left: 10px;">
        </span>
    </p>

    <!-- KETERANGAN + TOTAL INVOICE -->
    <table style="width: 100%; margin-top: 20px; margin-left: 10px; border: none;">
        <tr style="border: none;">
            <!-- Kiri -->
            <td style="width: 70%; vertical-align: top; text-align: left; border: none;">
                <p><b>Keterangan :</b></p>
                <ul>
                    <li>Komplain max. dalam 24 jam</li>
                    <li>Pengambilan barang mohon menunjukan faktur ini</li>
                    <li>Pembayaran Transfer melalui Rekening:
                        <ul>
                            <li>BCA 8360202969 a.n HANDRI PRASODJO</li>
                            <li>Mandiri 1350017266907 a.n HANDRI PRASODJO</li>
                            <li>Bank Jateng 2034157343 a.n HANDRI PRASODJO</li>
                        </ul>
                    </li>
                </ul>

                <div class="container" style="color: #003366 !important; margin-left: 125px;">
                    <div style="margin-top: 60px;">
                        <p style="margin: 0;"><b>Hormat Kami,</b></p>
                        <img src="{{ $logoPath }}" alt="Logo" style="margin-left: -60px" height="60">
                    </div>
                </div>
            </td>

            <!-- Kanan -->
            <td style="width: 30%; vertical-align: top; border: none;">
                <div
                    style="border: 2px solid black; padding: 10px; text-align: center; display: inline-block; min-width: 250px;">
                    <b>TOTAL INVOICE</b><br>
                    <span
                        style="font-size: 24px; font-weight: bold;">{{ number_format($transaction->total, 0, ',', '.') }}</span>
                </div>
            </td>
        </tr>
    </table>

    <div style="margin-top: 20px; margin-left: 10px; font-size: 12px;">
        <p><b>Nota dibuat oleh:</b> {{ Auth::user()->name ?? 'System' }}</p>
        <p><b>Tanggal cetak:</b> {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i:s') }}</p>
    </div>

</body>

<script>
    function terbilangJS(angka) {
        var satuan = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh",
            "Sebelas"
        ];

        angka = parseInt(angka);
        if (isNaN(angka) || angka < 0) return "Nol";

        if (angka < 12) {
            return satuan[angka];
        } else if (angka < 20) {
            return satuan[angka - 10] + " Belas";
        } else if (angka < 100) {
            return terbilangJS(Math.floor(angka / 10)) + " Puluh " + terbilangJS(angka % 10);
        } else if (angka < 200) {
            return "Seratus " + terbilangJS(angka - 100);
        } else if (angka < 1000) {
            return terbilangJS(Math.floor(angka / 100)) + " Ratus " + terbilangJS(angka % 100);
        } else if (angka < 2000) {
            return "Seribu " + terbilangJS(angka - 1000);
        } else if (angka < 1000000) {
            return terbilangJS(Math.floor(angka / 1000)) + " Ribu " + terbilangJS(angka % 1000);
        } else if (angka < 1000000000) {
            return terbilangJS(Math.floor(angka / 1000000)) + " Juta " + terbilangJS(angka % 1000000);
        } else if (angka < 1000000000000) {
            return terbilangJS(Math.floor(angka / 1000000000)) + " Miliar " + terbilangJS(angka % 1000000000);
        } else if (angka < 1000000000000000) {
            return terbilangJS(Math.floor(angka / 1000000000000)) + " Triliun " + terbilangJS(angka % 1000000000000);
        } else {
            return "Angka terlalu besar";
        }
    }

    // Set ke span
    let total = {{ $transaction->total ?? 0 }};
    document.getElementById('terbilangText').innerText = terbilangJS(total) + " Rupiah";
</script>

</html>
