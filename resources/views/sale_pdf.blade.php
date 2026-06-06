<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $sale->id }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .company-info { float: left; width: 50%; }
        .invoice-info { float: right; width: 40%; text-align: right; }
        .clear { clear: both; }
        .client-info { margin: 20px 0; padding: 10px; border: 1px solid #ddd; background: #f9f9f9; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #444; color: #fff; padding: 8px; text-align: left; }
        td { padding: 8px; border: 1px solid #ddd; }
        td.quantity-cell, td.amount-cell {
            white-space: nowrap;
            font-size: 10px;
            line-height: 1.15;
        }
        td.quantity-cell {
            white-space: normal;
        }
        .qty-main {
            display: block;
            font-size: 12px;
            font-weight: 700;
            line-height: 1.1;
        }
        .qty-sub {
            display: block;
            font-size: 9px;
            line-height: 1.1;
            color: #666;
            white-space: nowrap;
        }
        .qty-breakdown-title {
            display: block;
            margin-top: 6px;
            font-size: 9px;
            font-weight: 700;
            line-height: 1.15;
            color: #444;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .qty-ref {
            display: block;
            margin-top: 4px;
            font-size: 9.5px;
            line-height: 1.2;
            color: #666;
        }
        .stock-lines {
            display: block;
            margin-top: 4px;
            text-align: right;
        }
        .stock-line {
            display: block;
            font-size: 9px;
            line-height: 1.2;
            color: #666;
            white-space: normal;
        }
        .text-right { text-align: right; }
        .totals { margin-top: 20px; float: right; width: 30%; }
        .totals table td { border: none; padding: 5px 0; }
        .footer { margin-top: 50px; border-top: 1px solid #ddd; padding-top: 10px; font-size: 10px; text-align: center; }
        .amount-letter { margin-top: 30px; font-style: italic; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        @if($showCompanyInfo ?? true)
            <div class="company-info">
                <strong>{{ $company->name }}</strong><br>
                {{ $company->address }}<br>
                {{ $company->phone }}<br>
                {{ $company->email }}
            </div>
        @endif
        <div class="invoice-info">
            <h2 style="margin: 0; color: #444;">FACTURE</h2>
            â„–: {{ $sale->id }}<br>
            Date: {{ $sale->sale_date }}
        </div>
        <div class="clear"></div>
    </div>

    <div class="client-info">
        <strong>Client:</strong><br>
        {{ $sale->client->name }} {{ $sale->client->surname }}<br>
        {{ $sale->client->address }}<br>
        {{ $sale->client->phone }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th class="text-right">Prix Unitaire</th>
                <th class="text-right">QuantitÃ©</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->saleItems as $item)
            @php
                $showPrice = (bool) ($item->price_active ?? true);
                $quantityTotal = number_format((float) $item->quantity, 0);
                $stockLines = $saleItemStockReferences[$item->id] ?? [];
            @endphp
            <tr>
                <td>
                    {{ $item->product->name }}
                </td>
                <td class="text-right amount-cell">
                    {{ $showPrice ? number_format($item->price, 2) . ' Da' : '' }}
                </td>
                <td class="text-right quantity-cell">
                    <span class="qty-main">{{ $quantityTotal }} pcs</span>
                    <span class="qty-sub">Total requis</span>
                    @if(!empty($stockLines))
                        <span class="qty-breakdown-title">Répartition cartons</span>
                        <div class="stock-lines">
                            @foreach($stockLines as $line)
                                <span class="stock-line">• {{ $line['carton_breakdown'] }} - {{ $line['reference'] }}</span>
                            @endforeach
                        </div>
                    @endif
                </td>
                <td class="text-right amount-cell">
                    {{ $showPrice ? number_format($item->total_price, 2) . ' Da' : '' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td><strong>Total HT:</strong></td>
                <td class="text-right">{{ number_format($sale->total_amount, 2) }} Da</td>
            </tr>
            <tr>
                <td><strong>TVA (19%):</strong></td>
                <td class="text-right">{{ number_format($sale->total_amount * 0.19, 2) }} Da</td>
            </tr>
            <tr style="font-size: 14px; border-top: 2px solid #444;">
                <td><strong>Total TTC:</strong></td>
                <td class="text-right"><strong>{{ number_format($sale->total_amount * 1.19, 2) }} Da</strong></td>
            </tr>
        </table>
    </div>
    <div class="clear"></div>

    <div class="amount-letter">
        ArrÃªtÃ©e la prÃ©sente facture Ã  la somme de : {{ $amountLetter }}
    </div>

    <div class="footer">
        {{ $company->name }} - RC: {{ $company->NRC }} - NIF: {{ $company->NIF }} - AI: {{ $company->NART }}
    </div>
</body>
</html>
