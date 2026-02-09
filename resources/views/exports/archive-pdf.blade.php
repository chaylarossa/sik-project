<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Arsip Krisis</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #333;
            padding: 5px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0 0 5px 0;
        }
        .header p {
            margin: 0;
            color: #666;
        }
        .meta {
            margin-bottom: 15px;
        }
        .meta p {
            margin: 3px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        {{-- Header dihapus sesuai request --}}
    </div>

    <div class="meta">
        <p><strong>Tanggal Generate:</strong> {{ $generated_at->format('d M Y H:i:s') }}</p>
        <p><strong>Total Data:</strong> {{ $reports->count() }}</p>
        @if(isset($filters['date_from']) || isset($filters['date_to']))
        <p><strong>Periode:</strong> {{ $filters['date_from'] ?? 'Awal' }} s/d {{ $filters['date_to'] ?? 'Sekarang' }}</p>
        @endif
        @if(isset($filters['status']))
        <p><strong>Status Penanganan:</strong> {{ ucfirst(str_replace('_', ' ', $filters['status'])) }}</p>
        @endif
        @if(isset($filters['verification_status']))
        <p><strong>Status Verifikasi:</strong> {{ ucfirst($filters['verification_status']) }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Judul</th>
                <th width="15%">Jenis & Urgensi</th>
                <th width="20%">Lokasi</th>
                <th width="15%">Status</th>
                <th width="15%">Pelapor</th>
                <th width="10%">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $index => $report)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $report->title }}</td>
                <td>
                    {{ $report->crisisType->name ?? '-' }}<br>
                    <small>({{ $report->urgencyLevel->name ?? '-' }})</small>
                </td>
                <td>{{ $report->region->name ?? '-' }}</td>
                <td>
                    Penanganan: {{ ucfirst($report->status) }}<br>
                    Verifikasi: {{ ucfirst($report->verification_status) }}
                </td>
                <td>{{ $report->creator->name ?? '-' }}</td>
                <td>{{ $report->created_at->format('d/m/Y') }}<br>{{ $report->created_at->format('H:i') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center;">Tidak ada data laporan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
