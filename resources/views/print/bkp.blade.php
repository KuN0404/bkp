<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BKP - {{ $bkp->school->school_name ?? 'Nama Sekolah' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            background-color: #f8f9fa;
        }

        .bkp-container {
            border: 1px solid black;
            max-width: 800px;
            margin: 20px auto;
            background-color: white;
            padding: 0;
        }

        .bkp-header {
            text-align: center;
            padding: 7px 20px 0 20px;
        }

        .bkp-header h5 {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .bkp-content-padded {
            padding: 0.5px 20px;
        }

        .info-table td {
            border: none !important;
            padding: 1px 5px;
            vertical-align: top;
        }

        .amount-input {
            border: 1px solid black;
            background: transparent;
            text-align: center;
            font-weight: bold;
        }

        .signature-col {
            min-height: 50px;
            padding: 5px 5px;
        }


        .p-0-forced {
            padding: 0 !important;
            vertical-align: top;
        }

        .table-content-bottom {
            height: 100%;
            margin-bottom: 0 !important;
        }

        .table-content-bottom th {
            text-align: center;
            vertical-align: middle;
            padding: 2px 5px;
            line-height: 1.2;
            font-weight: normal;
        }

        .table-content-bottom td {
            text-align: center;
            vertical-align: middle;
            /* Padding vertikal (atas-bawah) dikurangi */
            padding: 1px 5px;
            line-height: 1.3;
        }

        .table-content-bottom thead {
            border-bottom: 1px solid black;
        }

        /* Properti height dihapus agar tinggi baris tidak dipaksa menjadi 35px */
        .table-content-bottom tbody td {
            /* height: 35px; */
        }

        @media print {
            body { margin: 0; padding: 0; background-color: white;}
            .bkp-container { margin: 0 auto; border: 1px solid black; max-width: 100%;}
        }
    </style>
</head>
<body onload="window.print()">
    <div class="bkp-container">
        <div class="bkp-header">
            <h5>PEMERINTAH KABUPATEN LAMPUNG UTARA</h5>
            <h5 class="text-decoration-underline">BUKTI KAS PENGELUARAN (BKP)</h5>
            <p class="mt-3">Nomor : ........................................</p>
        </div>

        <div class="bkp-content-padded">
            <table class="table info-table mb-3">
                <tr>
                    <td style="width: 240px;">Telah diterima dari bendaharawan</td>
                    <td style="width: 20px;">:</td>
                    <td class="fw-bold">
                        {{ $bkp->school->school_type ?? '' }}@if($bkp->school->school_status == 'Negeri')N
                        @else
S
                        @endif
                        {{ $bkp->school->school_name ?? '' }}
                    </td>
                </tr>
                <tr>
                    <td>Uang Sebesar</td>
                    <td>:</td>
                    <td class="fw-bold fst-italic" style=" border: 1px solid black !important; padding: 4px 8px !important;">{{ $bkp->sorted ?? 'Nol Rupiah' }}</td>
                </tr>
                <tr>
                    <td>Untuk Pembayaran</td>
                    <td>:</td>
                    <td>
                        {{ $bkp->activity->activity_name ?? '' }}
                    </td>
                </tr>
            </table>

            <div class="row mb-3 ms-2">
                <div class="col-4 d-flex align-items-center amount-input">
                    <span class="me-2 fs-5 fw-bold">Rp.</span>
                    <input type="text" class="form-control fw-bold flex-fill fs-5"
                           value="{{ number_format($bkp->nominal ?? 0, 0, ',', '.') }},-"
                           style="border: none; background: transparent; box-shadow: none;"
                           readonly>
                </div>
            </div>

            <div class="row text-center">
                <div class="col-4 signature-col">
                    <div class="mb-2">Mengetahui dan Menyetujui</div>
                    <div class="fw-bold mb-4">Kepala Sekolah</div>
                    <div style="height: 40px;"></div>
                    <div class="fw-bold text-decoration-underline text-uppercase">{{ $bkp->school->principal_name ?? '..........................................' }}</div>
                    <div class="small">NIP: {{ $bkp->school->principal_nip ?? '..........................................' }}</div>
                </div>
                <div class="col-4 signature-col">
                    <div class="mb-2">Dibayar tanggal, .......................</div>
                    <div class="fw-bold mb-4">Bendahara Sekolah</div>
                    <div style="height: 40px;"></div>
                    <div class="fw-bold text-decoration-underline text-uppercase">{{ $bkp->school->treasurer_name ?? '..........................................' }}</div>
                    <div class="small">NIP: {{ $bkp->school->treasurer_nip ?? '..........................................' }}</div>
                </div>
                <div class="col-4 signature-col">
                    <div class="mb-2">Kotabumi, ..............................</div>
                    <div class="fw-bold mb-4">Penerima</div>
                    <div style="height: 40px;"></div>
                    <div class="fw-bold text-decoration-underline text-uppercase">{{ $bkp->activity->director_name ?? '..........................................' }}</div>
                    <div class="small">DIREKTUR</div>
                </div>
            </div>
        </div>

        <table class="table table-borderless mb-0">
            <tbody>
                <tr>
                    <!-- Kolom 1: Diubah menggunakan Flexbox untuk tata letak yang lebih baik -->
                    <td style="width: 25%;" class="p-0-forced border-top border-black border-end">
                        <div style="height: 100%; display: flex; flex-direction: column; justify-content: space-between;">
                            <div class="text-center" style="border-bottom: 1px solid black; padding: 4.5px 5px; line-height: 1.2; font-weight: normal;">
                                Barang tsb telah diterima dengan baik
                            </div>
                            <div class="text-center" style="margin-top: 80px;">
                                <u>...................................</u>
                            </div>
                        </div>
                    </td>

                    <!-- Kolom 2: Diubah menggunakan Flexbox agar header sejajar dan konten di tengah -->
                    <td style="width: 25%;" class="p-0-forced border-top border-black border-end">
                        <div style="height: 100%; display: flex; flex-direction: column;">
                            <!-- Header yang sejajar -->
                            <div class="text-center" style="border-bottom: 1px solid black; padding: 14px 20px; line-height: 1.2; font-weight: normal;">
                                Pajak yang dipungut
                            </div>
                            <!-- Konten Pajak -->
                            <div style="flex-grow: 1; display: flex; flex-direction: column; justify-content: center; padding: 4px 5px;">
                                <table class="table table-borderless table-sm" style="margin-bottom: 0;">
                                    <tbody>
                                        <tr>
                                            <td class="text-start" style="width: 50%; padding: 1px;">PPN</td>
                                            <td class="text-start" style="padding: 1px;">Rp. {{ number_format($bkp->total_ppn, 0, ',', '.') ?? '............' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-start" style="padding: 1px;">PPh</td>
                                            <td class="text-start" style="padding: 1px;">Rp. {{ number_format($bkp->total_pph, 0, ',', '.') ?? '............' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-start fw-bold" style="padding: 1px;">Jumlah</td>
                                            <td class="text-start" style="padding: 1px;">Rp. {{ number_format($bkp->total_pajak, 0, ',', '.') ?? '............' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </td>

                    <!-- Kolom 3: Tetap sama -->
                    <td style="width: 50%;" class="p-0-forced border-top border-1 border-black">
                        <table class="table table-content-bottom table-sm">
                            <thead>
                                <tr>
                                    <th colspan="4">Pembebanan Terhadap Belanja</th>
                                </tr>
                                <tr>
                                    <th class="border-end border-black" style="width: 10%;">No.</th>
                                    <th class="border-end border-black" style="width: 25%;">Kode Rek.</th>
                                    <th class="border-end border-black">Uraian</th>
                                    <th style="width: 30%;">Jumlah Rupiah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border-end border-black">&nbsp;</td>
                                    <td class="border-end border-black">&nbsp;</td>
                                    <td class="border-end border-black">&nbsp;</td>
                                    <td class="text-start border-black">Rp.</td>
                                </tr>
                                <tr>
                                    <td class="border-end border-black">&nbsp;</td>
                                    <td class="border-end border-black">&nbsp;</td>
                                    <td class="border-end border-black">&nbsp;</td>
                                    <td class="text-start border-black">Rp.</td>
                                </tr>
                                <tr>
                                    <td class="border-end border-black">&nbsp;</td>
                                    <td class="border-end border-black">&nbsp;</td>
                                    <td class="border-end border-black">&nbsp;</td>
                                    <td class="text-start border-black">Rp.</td>
                                </tr>
                                <tr>
                                    <td class="border-end border-black">&nbsp;</td>
                                    <td class="border-end border-black">&nbsp;</td>
                                    <td class="border-end border-black">&nbsp;</td>
                                    <td class="text-start border-black">Rp.</td>
                                </tr>
                                 <tr>
                                    <td class="border-end border-bottom-0 border-black">&nbsp;</td>
                                    <td class="border-end border-bottom-0 border-black">&nbsp;</td>
                                    <td class="border-end border-bottom-0 border-black">&nbsp;</td>
                                    <td class="text-start">Rp.</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
