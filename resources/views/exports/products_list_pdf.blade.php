<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Products List</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #1f2937;
            margin: 0;
            padding: 28px;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 18px;
        }
        .header-left,
        .header-right {
            display: table-cell;
            vertical-align: top;
        }
        .header-right { text-align: right; }
        .company-name {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .muted {
            color: #6b7280;
            line-height: 1.5;
        }
        .title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .subtitle {
            color: #6b7280;
        }
        .meta {
            margin: 14px 0 18px;
            padding: 10px 12px;
            background: #f9fafb;
            border-radius: 8px;
            color: #4b5563;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead th {
            background: #111827;
            color: #fff;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 9px 8px;
            text-align: left;
        }
        tbody td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .num { text-align: right; white-space: nowrap; }
        .center { text-align: center; }
        .pill {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 999px;
            background: #f3f4f6;
            font-size: 10px;
            color: #374151;
            white-space: nowrap;
        }
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
            <div class="title">Products List</div>
            <div class="subtitle">Server export</div>
        </div>
    </div>

    <div class="meta">
        <strong>Search:</strong> {{ $searchValue !== '' ? $searchValue : 'All products' }}
        @if(!empty($departmentId))
            &nbsp;&nbsp;|&nbsp;&nbsp;<strong>Department:</strong> {{ $departmentId }}
        @endif
        <br>
        <strong>Total:</strong> {{ count($products) }} products
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:4%;">#</th>
                <th style="width:26%;">Product</th>
                <th style="width:14%;">Brand</th>
                <th style="width:12%;">SKU</th>
                <th style="width:10%;" class="center">Stock</th>
                <th style="width:12%;" class="center">Min</th>
                <th style="width:12%;" class="num">Price</th>
                <th style="width:10%;" class="center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $index => $product)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $product->name }}</strong><br>
                        <span class="muted">{{ $product->department?->name ?? 'No department' }}</span>
                    </td>
                    <td>{{ $product->brand ?: '-' }}</td>
                    <td class="nowrap">{{ $product->SKU ?: '-' }}</td>
                    <td class="center nowrap">{{ number_format((float) ($product->stock ?? 0), 0, ',', ' ') }}</td>
                    <td class="center nowrap">{{ number_format((float) ($product->min_stock_level ?? 0), 0, ',', ' ') }}</td>
                    <td class="num nowrap">{{ number_format((float) ($product->price ?? 0), 2, ',', ' ') }}</td>
                    <td class="center">
                        <span class="pill">{{ !empty($product->stockable) ? 'Stockable' : 'Simple' }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center muted" style="padding:18px;">No products found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
