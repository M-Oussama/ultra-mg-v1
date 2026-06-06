<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bon de Preparation {{ $sale->id }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #1e1e1e;
            background: #fff;
            padding: 40px 44px 36px;
        }

        .hdr { display: table; width: 100%; margin-bottom: 30px; }
        .hdr-l { display: table-cell; vertical-align: top; width: 58%; }
        .hdr-r { display: table-cell; vertical-align: top; width: 42%; text-align: right; }
        .logo { max-width: 150px; max-height: 52px; display: block; margin-bottom: 8px; }
        .co-name { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
        .co-line { font-size: 10.5px; color: #666; line-height: 1.8; }
        .doc-type { font-size: 11px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: #999; margin-bottom: 4px; }
        .doc-num  { font-size: 20px; font-weight: 700; color: #1e1e1e; margin-bottom: 10px; }
        .doc-info { font-size: 11px; color: #666; line-height: 2; }
        .doc-info strong { color: #1e1e1e; font-weight: 500; }
        hr { border: none; border-top: 1.5px solid #f0f0f0; margin: 0 0 28px; }
        .title-wrap { margin-bottom: 20px; }
        .title { font-size: 18px; font-weight: 700; color: #1e1e1e; margin-bottom: 4px; }

        table.items { width: 100%; border-collapse: collapse; margin-bottom: 28px; }
        table.items thead tr { border-bottom: 1.5px solid #e8e8e8; }
        table.items thead th {
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: #aaa;
            padding: 0 10px 10px;
            text-align: left;
        }
        table.items thead th.c { text-align: center; }
        table.items tbody tr { border-bottom: 1px solid #f4f4f4; }
        table.items tbody td {
            font-size: 11.5px;
            padding: 11px 10px;
            color: #1e1e1e;
            vertical-align: top;
        }
        table.items tbody td.c { text-align: center; }
        table.items tbody td.qty-total {
            white-space: nowrap;
            font-weight: 700;
        }
        table.items tbody td.stock-breakdown,
        table.items tbody td.stock-ref {
            color: #4b5563;
        }
        table.items tbody td.muted { color: #ccc; font-size: 11px; }

        .stock-lines {
            display: flex;
            flex-direction: column;
            gap: 4px;
            align-items: center;
            line-height: 1.35;
        }
        .stock-lines.left { align-items: flex-start; }
        .stock-line { display: block; }

        .note-wrap { margin-top: 28px; padding-top: 18px; border-top: 1px solid #f0f0f0; }
    </style>
</head>
<body>
    <div class="hdr">
        <div class="hdr-l">
            @if($showCompanyInfo)
                @if(!empty($logoAbsolutePath))
                    <img src="{{ $logoAbsolutePath }}" alt="Logo" class="logo">
                @endif
                <div class="co-name">{{ $departmentName }}</div>
                @if(!empty($departmentProfession))
                    <div class="co-line"><strong>{{ $departmentProfession }}</strong></div>
                @endif
                @if(!empty($departmentAddress))
                    <div class="co-line">{{ $departmentAddress }}</div>
                @endif
                @if(!empty($departmentEmail))
                    <div class="co-line">{{ $departmentEmail }}</div>
                @endif
                @if(!empty($departmentPhone))
                    <div class="co-line">{{ $departmentPhone }}</div>
                @endif
            @endif
        </div>
        <div class="hdr-r">
            <div class="doc-type">Bon de Preparation</div>
            <div class="doc-num">#{{ $sale->id }}</div>
            <div class="doc-info">Commande a preparer</div>
        </div>
    </div>

    <hr>

    <div class="title-wrap">
        <div class="title">Liste de preparation</div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th style="width:6%">#</th>
                <th style="width:29%">Designation</th>
                <th class="c" style="width:18%">Qte totale</th>
                <th class="c" style="width:35%">Repartition cartons</th>
                <th class="c" style="width:12%">Ref. stock</th>
            </tr>
        </thead>
        <tbody>
        @forelse($sale->saleItems as $index => $item)
            @php
                $quantityTotal = number_format((float) $item->quantity, 0, ',', ' ');
                $stockLines = $saleItemStockReferences[$item->id] ?? [];
            @endphp
            <tr>
                <td class="muted c">{{ $index + 1 }}</td>
                <td>{{ $item->product->name ?? '-' }}</td>
                <td class="c qty-total">{{ $quantityTotal }} pcs</td>
                <td class="c stock-breakdown">
                    @if(!empty($stockLines))
                        <div class="stock-lines">
                            @foreach($stockLines as $line)
                                <small class="stock-line">{{ $line['carton_breakdown'] }}</small>
                            @endforeach
                        </div>
                    @endif
                </td>
                <td class="c stock-ref">
                    @if(!empty($stockLines))
                        <div class="stock-lines">
                            @foreach($stockLines as $line)
                                <span class="stock-line">{{ $line['reference'] }}</span>
                            @endforeach
                        </div>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:16px; color:#bbb;">Aucun article</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="note-wrap"></div>
</body>
</html>
