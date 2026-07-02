<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture Sous-Traitant #{{ $invoice->fac_id }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            color: #000;
            background: #fff;
            line-height: 1.4;
            padding: 40px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            table-layout: fixed;
        }

        .header-table td {
            vertical-align: top;
            padding: 0;
        }

        .col-logo {
            width: 15%;
        }

        .col-company {
            width: 55%;
            padding-left: 10px;
            padding-right: 10px;
        }

        .col-legal {
            width: 30%;
            padding-top: 10px;
        }

        .logo {
            width: 110px;
            display: block;
        }

        .co-name {
            font-size: 11pt;
            font-weight: bold;
            color: #000;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        .co-desc {
            font-size: 8.5pt;
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .co-line {
            font-size: 8.5pt;
            font-weight: normal;
            color: #000;
            margin-bottom: 4px;
        }

        .legal-box {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 7.5pt;
            line-height: 2.1;
            color: #000;
            text-align: left;
            width: 100%;
        }

        .sep {
            border: none;
            border-top: 1px solid #bad5f0;
            margin: 12px 0 15px 0;
        }

        .row.contacts {
            width: 100%;
            margin-bottom: 20px;
            clear: both;
        }

        .row.contacts::after {
            content: "";
            display: table;
            clear: both;
        }

        .col {
            float: left;
        }

        .invoice-to {
            width: 75%;
            padding-right: 15px;
        }

        .right-col {
            width: 25%;
        }

        .info-row {
            font-size: 8.5pt;
            color: #000;
            line-height: 2.2;
            display: table;
            width: 100%;
            text-align: left;
        }

        .lbl {
            font-weight: bold;
            color: #000;
        }

        .client-legal-box {
            border: 1px solid #000;
            padding: 8px;
            font-size: 7.5pt;
            line-height: 1.9;
            color: #000;
            text-align: left;
            width: 100%;
            display: block;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            table-layout: fixed;
        }

        table.items th,
        table.items td {
            border: 1px solid #000;
            padding: 5px 8px;
            font-size: 8.5pt;
            color: #000;
            text-align: left;
        }

        table.items th {
            font-weight: normal;
            color: #555;
        }

        table.items th:nth-child(1) { width: 6%; }
        table.items th:nth-child(2) { width: 44%; }
        table.items th:nth-child(3) { width: 14%; }
        table.items th:nth-child(4) { width: 16%; }
        table.items th:nth-child(5) { width: 20%; }

        .totals-outer {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            table-layout: fixed;
        }

        .totals-outer td {
            padding: 0;
            vertical-align: top;
        }

        .col-spacer {
            width: 56%;
        }

        .col-totals {
            width: 44%;
        }

        .tot-table {
            width: 100%;
            border-collapse: collapse;
        }

        .tot-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            font-size: 8pt;
            color: #000;
            text-align: left;
        }

        .tot-lbl {
            width: 40%;
        }

        .tot-val {
            width: 60%;
            white-space: nowrap;
        }

        .amount-in-letters {
            margin-top: 15px;
            margin-bottom: 40px;
            font-size: 9pt;
            font-weight: bold;
            color: #000;
            line-height: 1.4;
        }

        .signature {
            text-align: center;
            font-size: 10pt;
            font-weight: bold;
            color: #000;
            margin-top: 25px;
        }
    </style>
</head>
<body>
    @php
        $cleanText = static function ($value): ?string {
            if ($value === null) {
                return null;
            }

            if (is_string($value)) {
                $value = trim($value);

                if ($value === '' || strcasecmp($value, 'null') === 0) {
                    return null;
                }

                return $value;
            }

            $value = trim((string) $value);

            if ($value === '' || strcasecmp($value, 'null') === 0) {
                return null;
            }

            return $value;
        };

        $firstText = static function (...$values) use ($cleanText): ?string {
            foreach ($values as $value) {
                $cleanValue = $cleanText($value);

                if ($cleanValue !== null) {
                    return $cleanValue;
                }
            }

            return null;
        };

        $companyName = $cleanText(data_get($company, 'name'));
        $companyDescription = $cleanText(data_get($company, 'description'));
        $companyAddress = $cleanText(data_get($company, 'address'));
        $companyCapital = $cleanText(data_get($company, 'capitale'));
        $companyPhone = $cleanText(data_get($company, 'phone'));
        $companyEmail = $cleanText(data_get($company, 'email'));

        $companyLegalLines = array_values(array_filter([
            ['label' => 'N&deg;AI', 'value' => $cleanText(data_get($company, 'NART'))],
            ['label' => 'N&deg;RC', 'value' => $cleanText(data_get($company, 'NRC'))],
            ['label' => 'N&deg;IS', 'value' => $cleanText(data_get($company, 'NIS'))],
            ['label' => 'N&deg;IF', 'value' => $cleanText(data_get($company, 'NIF'))],
        ], static fn (array $item): bool => $item['value'] !== null));

        $clientName = $cleanText(trim((string) data_get($invoice, 'client.name', '') . ' ' . (string) data_get($invoice, 'client.surname', '')));
        $clientAddress = $cleanText(data_get($invoice, 'client.address'));
        $clientProfession = $cleanText(data_get($invoice, 'client.profession'));
        $paymentType = (int) ($invoice->payment_type ?? 1);
        $paymentLabel = match ($paymentType) {
            2 => 'Cheque',
            3 => 'Versement',
            4 => 'Virement',
            default => 'Espece',
        };
        $paymentDisplay = match ($paymentLabel) {
            'Cheque' => 'Chèque',
            'Versement' => 'Versement',
            'Virement' => 'Virement',
            default => 'Espèce',
        };
        $chequeLabel = $cleanText(data_get($invoice, 'cheque_number'));
        $chequeBank = $cleanText(data_get($invoice, 'cheque.banque'));
        $chequeNumber = $cleanText(data_get($invoice, 'cheque.cheque_number'));
        $totalHT = (float) ($invoice->ht_amount ?: $invoice->amount);
        $tvaRate = (float) ($invoice->tva_rate ?: 19);
        $tvaAmount = (float) ($invoice->tva_amount ?: ($totalHT * $tvaRate / 100));
        $timbreRate = (float) ($invoice->timbre_rate ?: 0);
        $timbreAmount = (float) ($invoice->timbre_amount ?: 0);
        if ($timbreRate <= 0 && $timbreAmount > 0 && ($totalHT + $tvaAmount) > 0) {
            $timbreRate = ($timbreAmount / ($totalHT + $tvaAmount)) * 100;
        }
        $totalTTC = $totalHT + $tvaAmount + $timbreAmount;
        $factureRef = 'FAJ/' . \Carbon\Carbon::parse($invoice->date)->format('Y') . '/' . $invoice->fac_id;
        $invoiceDate = \Carbon\Carbon::parse($invoice->date)->format('d/m/Y');
        $tvaRateDisplay = rtrim(rtrim(number_format($tvaRate, 2, '.', ''), '0'), '.');
        $timbreRateDisplay = rtrim(rtrim(number_format($timbreRate, 2, '.', ''), '0'), '.');

        $clientLegalLines = array_values(array_filter([
            ['label' => 'N&deg;RC', 'value' => $firstText(data_get($invoice, 'client.NRC'), data_get($company, 'NRC'))],
            ['label' => 'N&deg;IF', 'value' => $firstText(data_get($invoice, 'client.NIF'), data_get($company, 'NIF'))],
            ['label' => 'N&deg;ART', 'value' => $firstText(data_get($invoice, 'client.NART'), data_get($company, 'NART'))],
            ['label' => 'N&deg;IS', 'value' => $firstText(data_get($invoice, 'client.NIS'), data_get($company, 'NIS'))],
        ], static fn (array $item): bool => $item['value'] !== null));
    @endphp

    <table class="header-table">
        <tbody>
            <tr>
                <td class="col-logo">
                    @if(!empty($logoDataUri))
                        <img src="{{ $logoDataUri }}" alt="Logo" class="logo">
                    @endif
                </td>

                <td class="col-company">
                    @if($companyName !== null)
                        <div class="co-name">{{ $companyName }}</div>
                    @endif
                    @if($companyDescription !== null)
                        <div class="co-desc">{{ $companyDescription }}</div>
                    @endif
                    @if($companyAddress !== null)
                        <div class="co-line">{{ $companyAddress }}</div>
                    @endif
                    @if($companyCapital !== null)
                        <div class="co-line">Capital Social: {{ number_format((float) $companyCapital, 0, ',', ' ') }} DA</div>
                    @endif
                    @if($companyPhone !== null)
                        <div class="co-line">Tel/Fax: {{ $companyPhone }}</div>
                    @endif
                    @if($companyEmail !== null)
                        <div class="co-line">Email: {{ $companyEmail }}</div>
                    @endif
                </td>

                <td class="col-legal">
                    @if($showCompanyInfo && !empty($companyLegalLines))
                        <div class="legal-box">
                            @foreach($companyLegalLines as $line)
                                {{ $line['label'] }}: {{ $line['value'] }}@if(!$loop->last)<br>@endif
                            @endforeach
                        </div>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <hr class="sep">

    <div class="row contacts">
        <div class="col invoice-to">
            <div class="info-row">
                <span class="lbl">Facture :</span> {{ $factureRef }}
            </div>
            <div class="info-row">
                <span class="lbl">Mode de paiement :</span> {{ $paymentDisplay }}
            </div>

            @if($paymentLabel === 'Cheque' && ($chequeLabel !== null || $chequeBank !== null || $chequeNumber !== null))
                <div class="info-row">
                    <span class="lbl">Chèque :</span>
                    @if($chequeBank !== null)
                        {{ $chequeBank }}@if($chequeNumber !== null) - @endif
                    @endif
                    @if($chequeNumber !== null)
                        {{ $chequeNumber }}
                    @elseif($chequeLabel !== null)
                        {{ $chequeLabel }}
                    @endif
                </div>
            @endif

            @if($clientName !== null)
                <div class="info-row">
                    <span class="lbl">Patient :</span> {{ $clientName }}
                </div>
            @endif

            @if($clientAddress !== null)
                <div class="info-row">
                    <span class="lbl">Adresse :</span> {{ $clientAddress }}
                </div>
            @endif

            @if($clientProfession !== null)
                <div class="info-row">
                    <span class="lbl">Activité :</span> {{ $clientProfession }}
                </div>
            @endif

            <div class="info-row">
                <span class="lbl">Sétif le :</span> {{ $invoiceDate }}
            </div>
        </div>

        <div class="col right-col">
           
        </div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>ID</th>
                <th>Description</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Prix</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->subCertifyInvoiceProducts as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ number_format($item->quantity, 0, '.', '') }}</td>
                    <td>{{ number_format($item->price, 2, '.', '') }} DA</td>
                    <td>{{ number_format($item->total, 2, '.', '') }} DA</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-outer">
        <tbody>
            <tr>
                <td class="col-spacer"></td>
                <td class="col-totals">
                    <table class="tot-table">
                        <tr>
                            <td class="tot-lbl">Montant HT</td>
                            <td class="tot-val">{{ number_format($totalHT, 2, '.', '') }} DA</td>
                        </tr>
                        <tr>
                            <td class="tot-lbl">TVA {{ $tvaRateDisplay }}%</td>
                            <td class="tot-val">{{ number_format($tvaAmount, 2, '.', '') }} DA</td>
                        </tr>
                        @if($timbreAmount > 0)
                        <tr>
                            <td class="tot-lbl">Timbre {{ $timbreRateDisplay }}%</td>
                            <td class="tot-val">{{ number_format($timbreAmount, 2, '.', '') }} DA</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="tot-lbl">Montant TTC</td>
                            <td class="tot-val">{{ number_format($totalTTC, 2, '.', '') }} DA</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="amount-in-letters">
        Arrêtée la présente facture à la somme de : {{ $amountLetter }}
    </div>

    <div class="signature">Cachet et Signature</div>
</body>
</html>
