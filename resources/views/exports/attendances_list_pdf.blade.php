<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendances List</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10.5px;
            color: #1f2937;
            margin: 0;
            padding: 28px;
        }
        .header { display: table; width: 100%; margin-bottom: 18px; }
        .header-left, .header-right { display: table-cell; vertical-align: top; }
        .header-right { text-align: right; }
        .company-name { font-size: 18px; font-weight: 700; margin-bottom: 4px; }
        .muted { color: #6b7280; line-height: 1.5; }
        .title { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
        .subtitle { color: #6b7280; }
        .meta {
            margin: 14px 0 18px;
            padding: 10px 12px;
            background: #f9fafb;
            border-radius: 8px;
            color: #4b5563;
        }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            background: #111827;
            color: #fff;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 6px;
            text-align: left;
        }
        tbody td { padding: 7px 6px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
        .center { text-align: center; }
        .nowrap { white-space: nowrap; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            @if($showCompanyInfo ?? true)
                <div class="company-name">{{ $company->name ?? 'Company' }}</div>
                <div class="muted">
                    {{ $company->address ?? '' }}<br>
                    {{ $company->phone ?? '' }}<br>
                    {{ $company->email ?? '' }}
                </div>
            @endif
        </div>
        <div class="header-right">
            <div class="title">Attendances List</div>
            <div class="subtitle">Server export</div>
        </div>
    </div>

    <div class="meta">
        <strong>Search:</strong> {{ $searchValue !== '' ? $searchValue : 'All attendances' }}
        <br>
        <strong>Total:</strong> {{ count($attendances) }} records
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:8%;">#</th>
                <th style="width:24%;">Month</th>
                <th style="width:18%;">Year</th>
                <th style="width:20%;">Employees</th>
                <th style="width:30%;">Reference</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $index => $attendance)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td><strong>{{ \Carbon\Carbon::createFromDate((int) $attendance->year, (int) $attendance->month, 1)->format('F') }}</strong></td>
                    <td class="nowrap">{{ $attendance->year }}</td>
                    <td class="nowrap">{{ $attendance->employees_count ?? 0 }}</td>
                    <td class="nowrap">{{ str_pad($attendance->month, 2, '0', STR_PAD_LEFT) }}/{{ $attendance->year }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="center muted" style="padding:18px;">No attendances found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
