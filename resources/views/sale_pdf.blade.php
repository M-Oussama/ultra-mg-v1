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
        .text-right { text-align: right; }
        .totals { margin-top: 20px; float: right; width: 30%; }
        .totals table td { border: none; padding: 5px 0; }
        .footer { margin-top: 50px; border-top: 1px solid #ddd; padding-top: 10px; font-size: 10px; text-align: center; }
        .amount-letter { margin-top: 30px; font-style: italic; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <strong>{{ $company->name }}</strong><br>
            {{ $company->address }}<br>
            {{ $company->phone }}<br>
            {{ $company->email }}
        </div>
        <div class="invoice-info">
            <h2 style="margin: 0; color: #444;">FACTURE</h2>
            №: {{ $sale->id }}<br>
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
                <th class="text-right">Quantité</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->saleItems as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td class="text-right">{{ number_format($item->price, 2) }} Da</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->total_price, 2) }} Da</td>
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
        Arrêtée la présente facture à la somme de : {{ $amountLetter }}
    </div>

    <div class="footer">
        {{ $company->name }} - RC: {{ $company->NRC }} - NIF: {{ $company->NIF }} - AI: {{ $company->NART }}
    </div>
</body>
</html>
