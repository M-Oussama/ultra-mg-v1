<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employees List</title>
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
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        thead th {
            background: #111827;
            color: #fff;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 6px;
            text-align: left;
        }
        tbody tr { height: 72px; }
        tbody td { padding: 10px 6px; border-bottom: 1px solid #e5e7eb; vertical-align: middle; }
        .center { text-align: center; }
        .nowrap { white-space: nowrap; }
        .active {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 999px;
            background: #ecfdf5;
            color: #047857;
            font-size: 9px;
            font-weight: 700;
        }
        .inactive {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 999px;
            background: #f3f4f6;
            color: #6b7280;
            font-size: 9px;
            font-weight: 700;
        }
        .sign-line {
            height: 54px;
            border-bottom: 1px solid #9ca3af;
        }
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
            <div class="title">{{ $title ?? 'Employees List' }}</div>
            <div class="subtitle">{{ $subtitle ?? 'Server export' }}</div>
        </div>
    </div>

    <div class="meta">
        <strong>Scope:</strong> {{ $scopeLabel ?? ($searchValue !== '' ? 'Search: ' . $searchValue : 'All employees') }}
        <br>
        <strong>Total:</strong> {{ count($employees) }} employees
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:10%;">ID</th>
                <th style="width:22%;">Name</th>
                <th style="width:22%;">Surname</th>
                <th style="width:23%;">Signature</th>
                <th style="width:23%;">Fingerprint</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $index => $employee)
                <tr>
                    <td class="center nowrap">{{ $employee->id }}</td>
                    <td><strong>{{ $employee->name ?: '-' }}</strong></td>
                    <td>{{ $employee->surname ?: '-' }}</td>
                    <td><div class="sign-line">&nbsp;</div></td>
                    <td><div class="sign-line">&nbsp;</div></td>
                </tr>
            @empty
                <tr><td colspan="5" class="center muted" style="padding:18px;">No employees found</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
