<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Suppliers List</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
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
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 9px 8px;
            text-align: left;
        }
        tbody td { padding: 8px; border-bottom: 1px solid #e5e7eb; vertical-align: top; }
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
            <div class="title">Suppliers List</div>
            <div class="subtitle">Server export</div>
        </div>
    </div>

    <div class="meta">
        <strong>Search:</strong> {{ $searchValue !== '' ? $searchValue : 'All suppliers' }}
        <br>
        <strong>Total:</strong> {{ count($suppliers) }} suppliers
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:4%;">#</th>
                <th style="width:28%;">Supplier</th>
                <th style="width:18%;">Phone</th>
                <th style="width:24%;">Email</th>
                <th style="width:14%;">City</th>
                <th style="width:12%;">NIF</th>
            </tr>
        </thead>
        <tbody>
            @forelse($suppliers as $index => $supplier)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ trim(($supplier->name ?? '') . ' ' . ($supplier->surname ?? '')) }}</strong>
                    </td>
                    <td class="nowrap">{{ $supplier->phone ?: '-' }}</td>
                    <td>{{ $supplier->email ?: '-' }}</td>
                    <td>{{ $supplier->city?->name ?: '-' }}</td>
                    <td class="nowrap">{{ $supplier->NIF ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="center muted" style="padding:18px;">No suppliers found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
