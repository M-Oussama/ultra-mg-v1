<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture Certifiée #{{ $invoice->fac_id }}</title>
    <style>
        /* ════════════════════════════
            RESET & BASE SETUP
           ════════════════════════════ */
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
            color: #000000;
            background: #fff;
            line-height: 1.4;
            padding: 40px;
        }

        /* ════════════════════════════
            HEADER SECTION
           ════════════════════════════ */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            table-layout: fixed; /* Prevents company info from shifting legal box */
        }
        
        .header-table td {
            vertical-align: top;
            padding: 0;
        }

        .col-logo {
            width: 18%;
        }
        
        .col-company {
            width: 70%;
            padding-left: 10px;
        }
        
        .col-legal {
            margin-top:10%;
            width: 30%;
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
            border: 1px solid #000000;
            padding: 6px 8px;
            font-size: 7.5pt;
            line-height: 2.1;
            color: #000;
            text-align: left;
            width: 100%;
        }

        /* Divider Rule */
        .sep {
            border: none;
            border-top: 1px solid #bad5f0;
            margin: 12px 0 15px 0;
        }

        /* ════════════════════════════
            INFO SECTION (DIV-BASED) - FIXED WRAPPING
           ════════════════════════════ */
        .row.contacts {
            width: 100%;
            margin-bottom: 20px;
            clear: both;
        }
        
        /* Clearfix fallback for PDF engines */
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
            margin-top:5%;
            width: 25%;
        }

        .info-row {
            font-size: 8.5pt;
            color: #000;
            line-height: 2.2;
          
            display: table;      /* Forces structural lining alignment */
            width: 100%;
              text-align:left !importants;
        }

        .lbl {
            font-weight: bold;
            color: #000;
           
        }

        .info-val {
            display: table-cell; /* Acts as right dynamic cell content wrapper */
            vertical-align: top;
            word-wrap: break-word;
            text-align:left !importants;
            float:left;
        }

        .client-legal-box {
            border: 1px solid #000000;
            padding: 8px;
            font-size: 7.5pt;
            line-height: 1.9;
            color: #000;
            text-align: left;
            width: 100%;
            display: block;
        }

        /* ════════════════════════════
            ITEMS MATRIX TABLE
           ════════════════════════════ */
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            table-layout: fixed;
        }
        
        table.items th,
        table.items td {
            border: 1px solid #000000;
            padding: 5px 8px;
            font-size: 8.5pt;
            color: #000;
            text-align: left;
        }
        
        table.items th {
            font-weight: normal;
            color: #555555;
        }
        
        table.items th:nth-child(1) { width: 6%; }
        table.items th:nth-child(2) { width: 44%; }
        table.items th:nth-child(3) { width: 14%; }
        table.items th:nth-child(4) { width: 16%; }
        table.items th:nth-child(5) { width: 20%; }

        /* ════════════════════════════
            TOTALS BOUNDING BOX
           ════════════════════════════ */
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
        
        .col-spacer { width: 56%; }
        .col-totals { width: 44%; }

        .tot-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .tot-table td {
            border: 1px solid #000000;
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

        /* ════════════════════════════
            CLOSING ASSIGNMENTS
           ════════════════════════════ */
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
        $clientName       = trim(($invoice->client->name ?? '') . ' ' . ($invoice->client->surname ?? ''));
        $clientAddress    = $invoice->client->address ?? '-';
        $clientProfession = $invoice->client->profession ?? '-';
        $paymentLabel     = $paymentLabel ?? 'Espece';
        $chequeLabel      = trim((string) ($invoice->cheque_number ?? ''));
        $chequeBank       = $invoice->cheque->banque ?? '';
        $chequeNumber     = $invoice->cheque->cheque_number ?? '';
        $totalHT          = (float) ($invoice->ht_amount ?: $invoice->amount);
        $tvaRate          = (float) ($invoice->tva_rate ?: 19);
        $tvaAmount        = (float) ($invoice->tva_amount ?: ($totalHT * $tvaRate / 100));
        $timbreAmount     = (float) ($invoice->timbre_amount ?: 0);
        $totalTTC         = $totalHT + $tvaAmount + $timbreAmount;
        $factureRef       = 'FAJ/' . \Carbon\Carbon::parse($invoice->date)->format('Y') . '/' . $invoice->fac_id;
        $invoiceDate      = \Carbon\Carbon::parse($invoice->date)->format('d/m/Y');
        $tvaRateDisplay   = rtrim(rtrim(number_format($tvaRate, 2, '.', ''), '0'), '.');
    @endphp

    <!-- Corporate Metadata Header -->
    <table class="header-table">
        <tbody>
            <tr>
                <td class="col-logo">
                    @if(!empty($logoDataUri))
                        <img src="{{ $logoDataUri }}" alt="Logo" class="logo">
                    @endif
                </td>

                <td class="col-company">
                    <div class="co-name">{{ $company->name }}</div>
                    @if(!empty($company->description))
                        <div class="co-desc">{{ $company->description }}</div>
                    @endif
                    @if(!empty($company->address))
                        <div class="co-line">{{ $company->address }}</div>
                    @endif
                    @if(!empty($company->capitale))
                        <div class="co-line">Capital Social: {{ number_format((float)$company->capitale, 0, ',', ' ') }} DA</div>
                    @endif
                    @if(!empty($company->phone))
                        <div class="co-line">Tel/Fax: {{ $company->phone }}</div>
                    @endif
                    @if(!empty($company->email))
                        <div class="co-line">Email: {{ $company->email }}</div>
                    @endif
                </td>

                <td class="col-legal">
                    <div class="legal-box">
                        N°AI: {{ $company->NART ?? '-' }}<br>
                        N°RC: {{ $company->NRC ?? '-' }}<br>
                        N°IS: {{ $company->NIS ?? '-' }}<br>
                        N°IF: {{ $company->NIF ?? '-' }}
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <hr class="sep">

    <!-- Div-Based Contacts Layout Row -->
    <div class="row contacts">
        <!-- Left Column -->
        <div class="col invoice-to">
            <div class="info-row">
               <span class="lbl"> Facture : </span> {{ $factureRef }}
      
            </div>
            <div class="info-row">
                <span class="lbl">Mode de paiement:</span>{{ $paymentLabel }}
               
            </div>
            
            @if($paymentLabel === 'Cheque' && $chequeLabel !== '')
                <div class="info-row">
                    <span class="lbl">Chèque N° :</span>  {{ $chequeLabel }}
                   
                </div>
            @endif
             @if($paymentLabel === 'Cheque' && $chequeBank !== ''&& $chequeNumber !== '')
                <div class="info-row">
                    <span class="lbl">Chèque N° :</span> {{$chequeBank}} - {{ $chequeNumber }}
                   
                </div>
            @endif
          
            <div class="info-row">
                <span class="lbl">Patient:</span> {{ $clientName !== '' ? $clientName : '-' }}
            
            </div>
            <div class="info-row">
                <span class="lbl">Adresse:</span>{{ $clientAddress }}
            </div>
                @if($clientProfession !== '' && $clientProfession !== null && $clientProfession !== NULL && $clientProfession !== 'NULL')
            <div class="info-row">
                <span class="lbl">Activité:</span> {{ $clientProfession }}
               
            </div>
            
            @endif
            <div class="info-row">
                <span class="lbl">Sétif le:</span>{{ $invoiceDate }}
               
            </div>
        </div>

        <!-- Right Column -->
        <div class="col right-col">
            <div class="client-legal-box">
                N°RC: {{ $invoice->client->NRC ?? $company->NRC ?? '-' }}<br>
                N°IF: {{ $invoice->client->NIF ?? $company->NIF ?? '-' }}<br>
                N°ART: {{ $invoice->client->NART ?? $company->NART ?? '-' }}<br>
                N°IS: {{ $invoice->client->NIS ?? $company->NIS ?? '-' }}
            </div>
        </div>
    </div>

    <!-- Line Items Breakdown Table Matrix -->
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
            @foreach($invoice->certifyInvoiceProducts as $index => $item)
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

    <!-- Financial Summary Display Module -->
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
                            <td class="tot-lbl">Timbre</td>
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

    <!-- Text Validation Segment -->
    <div class="amount-in-letters">
        Arrêtée la présente facture a la somme de : {{ $amountLetter }}
    </div>

    <!-- Official Validation Signature Footer -->
    <div class="signature">Cachet et Signature</div>

</body>
</html>