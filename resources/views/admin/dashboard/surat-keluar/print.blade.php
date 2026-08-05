<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Surat - {{ $surat->nomor_surat }}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 2.5cm;
            font-size: 12pt;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h3 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
            font-size: 12pt;
        }
        .header hr {
            border: 2px solid black;
            margin: 10px 0;
        }
        .content {
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 10px;
        }
        .signature {
            float: right;
            text-align: center;
            width: 300px;
            margin-right: -50px;
        }
        .signature p {
            margin: 50px 0 0 0;
        }
        .signature strong {
            display: block;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .clear {
            clear: both;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Print Button -->
    {{-- <div class="no-print" style="text-align: center; margin: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">
            Cetak Surat
        </button>
    </div> --}}

    <!-- Header Surat -->
    <div class="header">
        <h3>UNIVERSITAS BUDI LUHUR</h3>
        <h3>Laboratorium ICT Terpadu</h3>
        <p>Jl. Ciledug Raya Petukangan Utara Jakarta Selatan</p>
        <hr>
    </div>

    <!-- Nomor dan Tanggal Surat -->
    <div class="content">
        <table style="border: none;">
            <tr style="border: none;">
                <td style="border: none; width: 100px;">Nomor</td>
                <td style="border: none;">: {{ $surat->nomor_surat }}</td>
                <td style="border: none; text-align: right;">Jakarta, {{ \Carbon\Carbon::parse($surat->tanggal)->locale('id')->isoFormat('D MMMM Y') }}</td>
            </tr>
            <tr style="border: none;">
                <td style="border: none;">Perihal</td>
                <td style="border: none;" colspan="2">: {{ $surat->perihal }}</td>
            </tr>
        </table>
    </div>

    <!-- Konten Surat -->
    <div class="content">
        @switch($surat->jenis)
            @case('PP')
                <!-- Format Surat Pengajuan Parkir PKL -->
                <p>Kepada Yth.<br>
                {{ $surat->ditujukan_kepada }}<br>
                {{ $surat->jabatan_penerima }}</p>

                <p style="text-align: justify;">{{ $surat->isi }}</p>

                <h4 style="margin-top: 20px;">Daftar Pengaju Flat Rate Parkir ({{ $surat->jumlah_bulan }} bulan):</h4>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="text-align: center;">Nama</th>
                            <th style="text-align: center;">NIS</th>
                            <th style="text-align: center;">Nomor Polisi</th>
                            <th style="text-align: center;">Jenis Kendaraan</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @php
                            dd($surat);
                        @endphp --}}
                        @if(isset($surat->pengaju))
                            @php
                                $pengajuArray = json_decode($surat->pengaju, true);
                            @endphp
                            @foreach($pengajuArray as $index => $pengaju)
                                <tr>
                                    <td style="text-align: center;">{{ $index + 1 }}</td>
                                    <td style="text-align: center;">{{ $pengaju['nama'] ?? '' }}</td>
                                    <td style="text-align: center;">{{ $pengaju['npm'] ?? '' }}</td>
                                    <td style="text-align: center;">{{ $pengaju['nopol'] ?? '' }}</td>
                                    <td style="text-align: center;">{{ $pengaju['jenis_kendaraan'] ?? '' }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                @break

            @case('SA')
                <!-- Format Surat Sertifikat Asisten -->
                <p style="text-align: justify;">{{ $surat->isi }}</p>

                <h4 style="margin-top: 20px;">Daftar Penerima Sertifikat {{ $surat->nama_kegiatan }} Semester {{ $surat->semester }} {{ $surat->tahun_ajaran }}:</h4>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 5%;">No</th>
                            <th style="text-align: center;">Nama</th>
                            <th style="text-align: center;">NIM/NIS</th>
                            <th style="text-align: center;">Kegiatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @php
                            dd($surat);
                        @endphp --}}
                        @if(isset($surat->asisten))
                            @php
                                $asistenArray = json_decode($surat->asisten, true);
                            @endphp
                            @foreach($asistenArray as $index => $asisten)
                                <tr>
                                    <td style="text-align: center;">{{ $index + 1 }}</td>
                                    <td style="text-align: center;">{{ $asisten['nama'] ?? '' }}</td>
                                    <td style="text-align: center;">{{ $asisten['npm'] ?? '' }}</td>
                                    <td style="text-align: center;">{{ $asisten['matkul'] ?? '' }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                @break

            @case('U')
                <!-- Format Surat Undangan -->
                <p>Kepada Yth.<br>
                {{ $surat->ditujukan_kepada }}<br>
                {{ $surat->jabatan_penerima }}</p>

                <p style="text-align: justify;">{{ $surat->isi }}</p>

                <div style="margin-top: 20px;">
                    <table style="border: none;">
                        <tr style="border: none;">
                            <td style="border: none; width: 120px; vertical-align: top;">Acara</td>
                            <td style="border: none; vertical-align: top;">: {{ $surat->nama_kegiatan }}</td>
                        </tr>
                        <tr style="border: none;">
                            <td style="border: none; vertical-align: top;">Hari/Tanggal</td>
                            <td style="border: none; vertical-align: top;">: {{ \Carbon\Carbon::parse($surat->tanggal_kegiatan)->locale('id')->isoFormat('dddd, D MMMM Y') }}</td>
                        </tr>
                        <tr style="border: none;">
                            <td style="border: none; vertical-align: top;">Waktu</td>
                            <td style="border: none; vertical-align: top;">: {{ $surat->waktu_mulai }} - {{ $surat->waktu_selesai }} WIB</td>
                        </tr>
                        <tr style="border: none;">
                            <td style="border: none; vertical-align: top;">Tempat</td>
                            <td style="border: none; vertical-align: top;">: {{ $surat->tempat_kegiatan }}</td>
                        </tr>
                    </table>
                </div>
                @break

            @default
                <!-- Format Surat Default -->
                <p style="text-align: justify;">{{ $surat->isi }}</p>

                @if(isset($surat->lampiran_data) && is_array($surat->lampiran_data))
                    <h4 style="margin-top: 20px;">Lampiran:</h4>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th>Label</th>
                                <th>Isi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($surat->lampiran_data as $index => $lampiran)
                                @if(!empty($lampiran['label']) && !empty($lampiran['isi']))
                                    <tr>
                                        <td style="text-align: center;">{{ $index + 1 }}</td>
                                        <td>{{ $lampiran['label'] }}</td>
                                        <td>{{ $lampiran['isi'] }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                @endif
        @endswitch
    </div>

    <!-- Tanda Tangan -->
    <div class="footer">
        <div class="signature">
            <p style="margin-bottom: 5px;">Hormat kami,</p>
            <p style="margin-top: 80px;">
                <strong>Painem, S.Kom., M.Kom.</strong><span style="display:block; margin-top:-5px;">Kepala Laboratorium ICT Terpadu</span>
            </p>
        </div>
        <div class="clear"></div>
    </div>
</body>
</html>
