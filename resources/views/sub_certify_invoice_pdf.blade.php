<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture Sous-Traitant № {{ $invoice->fac_id }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #000; line-height: 1.4; }
        .header { margin-bottom: 30px; }
        .company-info { float: left; width: 50%; }
        .invoice-title { float: right; width: 45%; text-align: right; }
        .clear { clear: both; }
        .client-box { margin: 20px 0; border: 1px solid #000; padding: 15px; width: 50%; float: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #eee; padding: 8px; border: 1px solid #000; text-align: center; }
        td { padding: 8px; border: 1px solid #000; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totals-container { margin-top: 20px; }
        .totals-left { float: left; width: 50%; padding-top: 20px; }
        .totals-right { float: right; width: 40%; }
        .totals-right table td { padding: 4px; }
        .footer { position: fixed; bottom: 30px; width: 100%; text-align: center; font-size: 9px; border-top: 1px solid #000; padding-top: 10px; }
        .stamp-zone { margin-top: 40px; text-align: right; padding-right: 50px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <h1 style="margin: 0; font-size: 18px;">{{ $company->name }}</h1>
            {{ $company->address }}<br>
            Tél: {{ $company->phone }}<br>
            NRC: {{ $company->NRC }} - NIF: {{ $company->NIF }}<br>
            AI: {{ $company->NART }} - CB: {{ $company->NIS }}
        </div>
        <div class="invoice-title">
            <h2 style="margin: 0;">FACTURE SOUS-TRAITANT</h2>
            №: {{ $invoice->fac_id }}/{{ date('Y', strtotime($invoice->date)) }}<br>
            Date: {{ date('d/m/Y', strtotime($invoice->date)) }}
        </div>
        <div class="clear"></div>
    </div>

    <div class="client-box">
        <strong>CLIENT:</strong><br>
        {{ $invoice->client->name }}<br>
        {{ $invoice->client->address }}<br>
        NIF: {{ $invoice->client->NIF }}
    </div>
    <div class="clear"></div>

    <table style="margin-bottom: 10px;">
        <tr>
            <td style="border: none; padding: 0;"><strong>Référence Facture Parent:</strong> № {{ $invoice->certify_invoice_id }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="50%">Désignation</th>
                <th width="10%">Qté</th>
                <th width="20%">P.U (HT)</th>
                <th width="20%">Montant HT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->subCertifyInvoiceProducts as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->price, 2) }}</td>
                <td class="text-right">{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-container">
        <div class="totals-left">
            Arrêtée la présente facture à la somme de :<br>
            <strong>{{ $amountLetter }}</strong>
            @if($invoice->cheque_number)
            <br><br>Paiement par chèque №: {{ $invoice->cheque_number }}
            @endif
        </div>
        <div class="totals-right">
            <table style="border: 1px solid #000;">
                <tr>
                    <td>Total HT:</td>
                    <td class="text-right">{{ number_format($invoice->ht_amount ?: $invoice->amount, 2) }} Da</td>
                </tr>
                <tr>
                    <td>TVA ({{ $invoice->tva_rate ?: 19 }}%):</td>
                    <td class="text-right">{{ number_format($invoice->tva_amount ?: ($invoice->amount * 0.19), 2) }} Da</td>
                </tr>
                @if($invoice->timbre_amount)
                <tr>
                    <td>Timbre:</td>
                    <td class="text-right">{{ number_format($invoice->timbre_amount, 2) }} Da</td>
                </tr>
                @endif
                <tr style="background: #eee; font-weight: bold;">
                    <td>TOTAL TTC:</td>
                    <td class="text-right">{{ number_format($invoice->amount + ($invoice->tva_amount ?: ($invoice->amount * 0.19)) + ($invoice->timbre_amount ?: 0), 2) }} Da</td>
                </tr>
            </table>
        </div>
        <div class="clear"></div>
    </div>

    <div class="stamp-zone">
        <strong>Cachet et Signature</strong>
    </div>

    <div class="footer">
        {{ $company->name }} - Capital : {{ $company->capitale }} Da<br>
        {{ $company->email }}
    </div>
</body>
</html>
