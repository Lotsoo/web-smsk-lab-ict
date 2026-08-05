<!DOCTYPE html>
<html>

<head>
    <title>Surat Keluar - {{ $surat->nomor_surat }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        @page {
            margin: 2.5cm 2cm;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            line-height: 1.5;
            font-size: 12pt;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }

        .logo {
            max-height: 80px;
            margin-bottom: 10px;
        }

        .institution {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .address {
            font-size: 10pt;
            margin-bottom: 10px;
        }

        .title {
            text-align: center;
            margin-bottom: 30px;
        }

        .title h3 {
            text-transform: uppercase;
            font-size: 14pt;
            margin-bottom: 10px;
            text-decoration: underline;
        }

        .letter-number {
            margin-bottom: 5px;
        }

        .letter-date {
            margin-bottom: 20px;
        }

        .content {
            text-align: justify;
            margin-bottom: 50px;
            line-height: 1.6;
        }

        .signature {
            float: right;
            width: 40%;
            text-align: center;
        }

        .signature-name {
            margin-top: 80px;
            font-weight: bold;
            text-decoration: underline;
        }

        .signature-title {
            font-size: 10pt;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .lampiran {
            margin-top: 30px;
            page-break-before: always;
        }

        .lampiran-title {
            font-weight: bold;
            margin-bottom: 15px;
            font-size: 14pt;
        }

        .lampiran-table {
            width: 100%;
            border-collapse: collapse;
        }

        .lampiran-table th,
        .lampiran-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .lampiran-table th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <!-- nama lab -->
    <div class="header">
        <div class="institution">NAMA INSTITUSI</div>
        <div class="address">Jalan Contoh No. 123, Kota, Provinsi, Kode Pos</div>
        <div>Email: contoh@email.com | Telepon: (021) 1234-5678</div>
    </div>

    <!-- judul surat -->
    <div class="title">
        <h3>{{ $surat->perihal }}</h3>
        <div class="letter-number">Nomor: {{ $surat->nomor_surat }}</div>
        <div class="letter-date">Tanggal: {{ \Carbon\Carbon::parse($surat->tanggal)->translatedFormat('d F Y') }}</div>
    </div>

    <!-- Isi Surat -->
    <div class="content">
        {!! nl2br(e($surat->isi)) !!}
    </div>

    <!-- Tanda Tangan -->
    <div class="signature">
        <div>Kota, {{ \Carbon\Carbon::parse($surat->tanggal)->translatedFormat('d F Y') }}</div>
        <div>Pejabat yang berwenang,</div>
        <div class="signature-name">NAMA PEJABAT</div>
        <div class="signature-title">Jabatan</div>
    </div>

    <!-- Lampiran (jika ada) -->
    @if (isset($lampiran) && $lampiran->count() > 0)
        <div class="lampiran">
            <div class="lampiran-title">LAMPIRAN</div>
            <table class="lampiran-table">
                <thead>
                    <tr>
                        <th width="30%">Label</th>
                        <th width="70%">Isi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lampiran as $item)
                        <tr>
                            <td>{{ $item->label }}</td>
                            <td>{{ $item->isi }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        Dokumen ini dicetak pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB
    </div>
</body>

</html>
