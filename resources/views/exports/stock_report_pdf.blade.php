<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stock Report</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10.5px;
            color: #1f2937;
            margin: 0;
            padding: 24px;
        }
        .header { display: table; width: 100%; margin-bottom: 16px; }
        .header-left, .header-right { display: table-cell; vertical-align: top; }
        .header-right { text-align: right; }
        .company-name { font-size: 18px; font-weight: 700; margin-bottom: 4px; }
        .muted { color: #6b7280; line-height: 1.5; }
        .title { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
        .subtitle { color: #6b7280; }
        .meta {
            margin: 12px 0 16px;
            padding: 10px 12px;
            background: #f9fafb;
            border-radius: 8px;
            color: #4b5563;
        }
        .summary {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin-bottom: 14px;
        }
        .summary td {
            width: 25%;
            background: #f9fafb;
            border-radius: 10px;
            padding: 10px 12px;
            vertical-align: top;
        }
        .summary .label {
            display: block;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #6b7280;
            margin-bottom: 4px;
        }
        .summary .value {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
        }
        h2 {
            font-size: 14px;
            margin: 16px 0 8px;
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
        .num { text-align: right; white-space: nowrap; }
        .center { text-align: center; }
        .nowrap { white-space: nowrap; }
        .small { font-size: 9px; color: #6b7280; line-height: 1.4; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            @if($showCompanyInfo ?? true)
                <div class="company-name">{{ $company->name ?? 'Company' }}</div>
                <div class="muted">
                    {{ $departmentAddress ?? $company->address ?? '' }}<br>
                    {{ $departmentPhone ?? $company->phone ?? '' }}<br>
                    {{ $departmentEmail ?? $company->email ?? '' }}
                </div>
            @endif
        </div>
        <div class="header-right">
            <div class="title">Stock Report</div>
            <div class="subtitle">{{ $departmentName ?? 'All departments' }}</div>
        </div>
    </div>

    <div class="meta">
        <strong>Search:</strong> {{ $search !== '' ? $search : 'All items' }}
        <br>
        <strong>Containers:</strong> {{ number_format((float) ($summary['container_count'] ?? 0), 0, ',', ' ') }}
        &nbsp;&nbsp;|&nbsp;&nbsp;<strong>Products:</strong> {{ number_format((float) ($summary['product_count'] ?? 0), 0, ',', ' ') }}
    </div>

    <table class="summary">
        <tr>
            <td>
                <span class="label">Received</span>
                <span class="value">{{ number_format((float) ($summary['received_quantity'] ?? 0), 0, ',', ' ') }}</span>
            </td>
            <td>
                <span class="label">Sold</span>
                <span class="value">{{ number_format((float) ($summary['sold_quantity'] ?? 0), 0, ',', ' ') }}</span>
            </td>
            <td>
                <span class="label">Remaining</span>
                <span class="value">{{ number_format((float) ($summary['remaining_quantity'] ?? 0), 0, ',', ' ') }}</span>
            </td>
            <td>
                <span class="label">Department</span>
                <span class="value">{{ $businessId > 0 ? $businessId : 'All' }}</span>
            </td>
        </tr>
    </table>

    <h2>Products</h2>
    <table>
        <thead>
            <tr>
                <th style="width:4%;">#</th>
                <th style="width:28%;">Product</th>
                <th style="width:11%;" class="num">Received</th>
                <th style="width:11%;" class="num">Sold</th>
                <th style="width:11%;" class="num">Remaining</th>
                <th style="width:11%;" class="center">Batches</th>
                <th style="width:24%;">Catalog qty</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $index => $product)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $product['product_name'] ?? 'Product' }}</strong>
                    </td>
                    <td class="num">{{ number_format((float) ($product['received_quantity'] ?? 0), 0, ',', ' ') }}</td>
                    <td class="num">{{ number_format((float) ($product['sold_quantity'] ?? 0), 0, ',', ' ') }}</td>
                    <td class="num">{{ number_format((float) ($product['fifo_remaining_quantity'] ?? 0), 0, ',', ' ') }}</td>
                    <td class="center">{{ number_format((float) ($product['container_count'] ?? 0), 0, ',', ' ') }}</td>
                    <td>
                        {{ number_format((float) ($product['catalog_quantity'] ?? 0), 0, ',', ' ') }}
                        <div class="small">Current stock in catalog</div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center muted" style="padding:18px;">No products found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Containers</h2>
    <table>
        <thead>
            <tr>
                <th style="width:5%;">#</th>
                <th style="width:14%;">Reference</th>
                <th style="width:15%;">Date</th>
                <th style="width:12%;" class="num">Received</th>
                <th style="width:12%;" class="num">Sold</th>
                <th style="width:12%;" class="num">Remaining</th>
                <th style="width:30%;">Items</th>
            </tr>
        </thead>
        <tbody>
            @forelse($containers as $index => $container)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="nowrap">{{ $container['reference'] ?? '-' }}</td>
                    <td class="nowrap">{{ $container['supply_date'] ?? '-' }}</td>
                    <td class="num">{{ number_format((float) ($container['received_total'] ?? 0), 0, ',', ' ') }}</td>
                    <td class="num">{{ number_format((float) ($container['sold_total'] ?? 0), 0, ',', ' ') }}</td>
                    <td class="num">{{ number_format((float) ($container['remaining_total'] ?? 0), 0, ',', ' ') }}</td>
                    <td>
                        @if(!empty($container['items']))
                            @foreach($container['items'] as $item)
                                <div class="small">
                                    {{ $item['product_name'] ?? 'Product' }} -
                                    {{ number_format((float) ($item['remaining_quantity'] ?? 0), 0, ',', ' ') }} left
                                    @if(!empty($item['reference']))
                                        (#{{ $item['reference'] }})
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <span class="muted">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center muted" style="padding:18px;">No containers found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
