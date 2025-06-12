<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BKP - {{ $bkp->school->school_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
        }

        .bkp-container {
            border: 2px solid black;
            max-width: 800px;
            margin: 20px auto;
        }

        .bkp-header {
            text-align: center;
            border-bottom: 1px solid black;
            padding: 15px;
        }

        .bkp-header h4 {
            text-decoration: underline;
            font-weight: bold;
            margin: 0;
        }

        .info-table td {
            border: none !important;
            padding: 2px 5px;
            vertical-align: top;
        }

        .amount-input {
            border: none;
            border-bottom: 2px solid black;
            background: transparent;
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
        }

        .signature-section {
            border-top: 1px solid black;
            border-bottom: 1px solid black;
        }

        .signature-col {
            border-right: 1px solid black;
            min-height: 180px;
            padding: 15px 10px;
        }

        .signature-col:last-child {
            border-right: none;
        }

        .signature-name {
            text-decoration: underline;
            font-weight: bold;
        }

        .detail-table {
            border: 1px solid black;
        }

        .detail-table th,
        .detail-table td {
            border: 1px solid black !important;
            text-align: center;
            vertical-align: middle;
            padding: 8px 5px;
        }

        .detail-table td:nth-child(3) {
            text-align: left;
        }

        .detail-table td:nth-child(4) {
            text-align: right;
        }

        .tax-total-section {
            border-left: 1px solid black;
        }

        .tax-input {
            border: none;
            border-bottom: 1px solid black;
            background: transparent;
            text-align: right;
            width: 100%;
        }

        .total-border {
            border-top: 2px solid black;
            font-weight: bold;
            font-size: 14pt;
        }

        @media print {
            body { margin: 0; padding: 0; }
            .bkp-container { margin: 10px auto; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container-fluid">
        <div class="bkp-container">
            <!-- Header -->
            <div class="bkp-header">
                <h4>BUKTI KAS PENGELUARAN (BKP)</h4>
            </div>

            <!-- Content -->
            <div class="p-3">
                <!-- Info Section -->
                <table class="table info-table mb-4">
                    <tr>
                        <td style="width: 200px;">Telah diterima dari bendaharawan</td>
                        <td style="width: 20px;">:</td>
                        <td class="fw-bold">Sekolah</td>
                    </tr>
                    <tr>
                        <td>Uang Sebesar</td>
                        <td>:</td>
                        <td class="fw-bold">{{ $bkp->amount_in_words ?? 'Satu Juta Dua Ratus Tujuh Ribu Rupiah' }}</td>
                    </tr>
                    <tr>
                        <td>Untuk Pembayaran</td>
                        <td>:</td>
                        <td class="fw-bold">
                            {{ $bkp->description ?? 'Penggandaan Soal Penilaian Akhir Semester (PAS) Genap' }}<br>
                            T.A. {{ $bkp->fiscal_year ?? date('Y') }}/{{ $bkp->fiscal_year ? $bkp->fiscal_year + 1 : date('Y') + 1 }}
                        </td>
                    </tr>
                </table>

                <!-- Amount Section -->
                <div class="row mb-4">
                    <div class="col-12 d-flex align-items-center">
                        <span class="me-2 fs-5">Rp.</span>
                        <input type="text" class="form-control amount-input flex-fill"
                               value="{{ number_format($bkp->amount ?? 1207000, 0, ',', '.') }}" readonly>
                    </div>
                </div>

                <!-- Signatures Section -->
                <div class="row signature-section mb-3">
                    <div class="col-4 signature-col text-center">
                        <div class="mb-2">Mengetahui dan Menyetujui</div>
                        <div class="fw-bold mb-4">Kepala Sekolah</div>
                        <div style="height: 60px;"></div>
                        <div class="signature-name mb-1">{{ $bkp->school->principal_name ?? 'Nama' }}</div>
                        <div class="small">NIP: {{ $bkp->school->principal_nip ?? '123456789876545678990' }}</div>
                        <div class="mt-3 small fst-italic">Barang tsb telah diterima dengan baik</div>
                    </div>
                    <div class="col-4 signature-col text-center">
                        <div class="mb-2">Dibayar tanggal ........................</div>
                        <div class="fw-bold mb-4">Bendahara Sekolah</div>
                        <div style="height: 60px;"></div>
                        <div class="signature-name mb-1">{{ $bkp->school->treasurer_name ?? 'Nama' }}</div>
                        <div class="small">NIP: {{ $bkp->school->treasurer_nip ?? '123456789876545678990' }}</div>
                        <div class="mt-3 small fst-italic">Telah yang dipungut</div>
                    </div>
                    <div class="col-4 signature-col text-center">
                        <div class="mb-2">Kotabumi, {{ \Carbon\Carbon::parse($bkp->date ?? now())->translatedFormat('d F Y') }}</div>
                        <div class="fw-bold mb-4">Penerima</div>
                        <div style="height: 60px;"></div>
                        <div class="signature-name mb-1">{{ $bkp->recipient_name ?? '................................' }}</div>
                        <div class="small">&nbsp;</div>
                        <div class="mt-3 small fst-italic">Penerimaan Terhadap Belanja</div>
                    </div>
                </div>

                <!-- Details Table -->
                <div class="row">
                    <div class="col-8">
                        <table class="table detail-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 8%;">No.</th>
                                    <th style="width: 25%;">Kode Rekening</th>
                                    <th style="width: 45%;">Uraian</th>
                                    <th style="width: 22%;">Jumlah Rupiah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($bkp->details) && $bkp->details->count() > 0)
                                    @foreach($bkp->details as $index => $detail)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $detail->account_code ?? '' }}</td>
                                        <td>{{ $detail->description ?? '' }}</td>
                                        <td>Rp. {{ number_format($detail->amount ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                    @for($i = $bkp->details->count(); $i < 5; $i++)
                                    <tr style="height: 40px;">
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    @endfor
                                @else
                                    @for($i = 0; $i < 5; $i++)
                                    <tr style="height: 40px;">
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    @endfor
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Tax and Total Section -->
                    <div class="col-4 tax-total-section p-3">
                        <div class="mb-2 d-flex align-items-center">
                            <span class="me-2" style="width: 40px;">PPN</span>
                            <span class="me-2">Rp.</span>
                            <input type="text" class="tax-input" value="{{ number_format($bkp->ppn ?? 0, 0, ',', '.') }}" readonly>
                        </div>
                        <div class="mb-3 d-flex align-items-center">
                            <span class="me-2" style="width: 40px;">PPh</span>
                            <span class="me-2">Rp.</span>
                            <input type="text" class="tax-input" value="{{ number_format($bkp->pph ?? 0, 0, ',', '.') }}" readonly>
                        </div>
                        <div class="total-border pt-2 d-flex align-items-center">
                            <span class="me-2" style="width: 40px;">Jumlah</span>
                            <span class="me-2">Rp.</span>
                            <div class="flex-fill text-end">{{ number_format($bkp->total_amount ?? $bkp->amount ?? 1207000, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
