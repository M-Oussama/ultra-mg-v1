<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            background: #fff;
            padding: 20px 24px;
            line-height: 1.5;
        }

        /* ── HEADER using table layout ── */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .header-table td { vertical-align: top; padding: 0; border: none; }
        .logo-cell { width: 75px; padding-right: 12px !important; }
        .logo-placeholder {
            width: 70px; height: 70px;
            border: 1px solid #bbb;
            border-radius: 5px;
            font-size: 8px; color: #aaa;
            text-align: center;
            line-height: 70px;
        }
        .company-cell { vertical-align: top; }
        .company-name { font-weight: bold; font-size: 13px; text-transform: uppercase; margin-bottom: 2px; }
        .company-tagline { font-size: 10px; margin-bottom: 7px; max-width: 230px; line-height: 1.3; }
        .company-info { font-size: 10px; line-height: 1.7; }
        .id-box-cell { width: 210px; padding-left: 10px !important; vertical-align: top; }
        .id-box {
            border: 1px solid #000;
            padding: 7px 11px;
            font-size: 10px;
            line-height: 1.9;
        }

        /* ── BLUE LINE ── */
        .divider { border: none; border-top: 1.5px solid #2980b9; margin: 12px 0; }

        /* ── META + CLIENT using table layout ── */
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .meta-table td { vertical-align: top; padding: 0; border: none; }
        .meta-left-cell { vertical-align: top; font-size: 11px; line-height: 1.9; }
        .meta-right-cell { width: 210px; padding-left: 10px !important; vertical-align: top; }

        /* ── ITEMS TABLE ── */
        table.items { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.items th {
            border: 1px solid #000;
            padding: 5px 8px;
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            background: #fff;
        }
        table.items th.left { text-align: left; }
        table.items th.right { text-align: right; }
        table.items td { border: 1px solid #000; padding: 5px 8px; font-size: 11px; }
        table.items td.center { text-align: center; }
        table.items td.right  { text-align: right; }

        /* ── TOTALS ── */
        .totals-wrap { width: 100%; margin-top: 10px; }
        table.totals { border-collapse: collapse; width: 290px; float: right; }
        table.totals td { border: 1px solid #000; padding: 5px 10px; font-size: 11px; }
        table.totals .val { text-align: right; white-space: nowrap; }
        table.totals .bold td { font-weight: bold; }
        .clear { clear: both; }

        /* ── FOOTER ── */
        .amount-words { margin-top: 28px; font-weight: bold; font-size: 11px; line-height: 1.5; }
        .signature { margin-top: 60px; text-align: center; font-weight: bold; font-size: 13px; }
    </style>
</head>
<body>
    @php
        $companyName = data_get($company, 'name', '');
        $companyEmail = data_get($company, 'email', '');
        $companyNart = data_get($company, 'NART', '');
        $companyNrc = data_get($company, 'NRC', '');
        $companyNis = data_get($company, 'NIS', '');
        $companyNif = data_get($company, 'NIF', '');

        $client = $invoice->client ?? null;
        $clientName = trim((string) data_get($client, 'name', '') . ' ' . data_get($client, 'surname', ''));
        $clientName = $clientName !== '' ? $clientName : 'N/A';
        $clientAddress = data_get($client, 'address', 'N/A');
        $clientRc = data_get($client, 'NRC', 'N/A');
        $clientNif = data_get($client, 'NIF', 'N/A');
        $clientArt = data_get($client, 'NART', 'N/A');
        $clientIs = data_get($client, 'NIS', 'N/A');
        $invoiceDate = data_get($invoice, 'invoice_date', '');
        $invoiceNumber = data_get($invoice, 'id', '');
        $invoiceItems = $invoice->items ?? collect();
        $totalAmount = (float) data_get($invoice, 'total_amount', 0);
    @endphp

    <!-- HEADER -->
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <!-- Replace div below with: <img src="logo.png" width="70"> -->
                <div class="logo-placeholder">LOGO</div>
            </td>
            <td class="company-cell">
                <div class="company-name">{{ $companyName }}</div>
                <div class="company-tagline">FABRICATION DES PRODUITS DE BLANCHISSANTS ET CONNEXES</div>
                <div class="company-info">
                    Lot N° 34 Section 6 Groupe 51 KASR EL ABTAL<br>
                    Capital Social: 11 000 000 DA<br>
                    Email: {{ $companyEmail }}
                </div>
            </td>
            <td class="id-box-cell">
                <div class="id-box">
                    N°AI: {{ $companyNart }}<br>
                    N°RC: {{ $companyNrc }}<br>
                    N°IS: {{ $companyNis }}<br>
                    N°IF: {{ $companyNif }}
                </div>
            </td>
        </tr>
    </table>

    <!-- BLUE SEPARATOR -->
    <hr class="divider">

    <!-- META + CLIENT IDs -->
    <table class="meta-table">
        <tr>
            <td class="meta-left-cell">
                <strong>Facture :</strong> {{ $invoiceNumber }}<br>
                <strong>Mode de paiement:</strong> Paiement a terme<br>
                <strong>Patient/Client:</strong> {{ $clientName }}<br>
                <strong>address:</strong> {{ $clientAddress }}<br>
                <strong>Sétif le:</strong> {{ $invoiceDate }}
            </td>
            <td class="meta-right-cell">
                <div class="id-box">
                    N°RC: {{ $clientRc }}<br>
                    N°IF: {{ $clientNif }}<br>
                    N°ART: {{ $clientArt }}<br>
                    N°IS: {{ $clientIs }}
                </div>
            </td>
        </tr>
    </table>

    <!-- ITEMS TABLE -->
    <table class="items">
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="44%" class="left">Description</th>
                <th width="13%">Quantité</th>
                <th width="18%" class="right">Prix unitaire</th>
                <th width="20%" class="right">Prix</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoiceItems as $index => $item)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ data_get($item, 'product_name') ?: data_get($item, 'product.name', 'Produit') }}</td>
                <td class="center">{{ data_get($item, 'quantity', 0) }}</td>
                <td class="right">{{ number_format((float) data_get($item, 'price', 0), 2, '.', '') }} DA</td>
                <td class="right">{{ number_format((float) data_get($item, 'total_price', 0), 2, '.', '') }} DA</td>
            </tr>
            @empty
            <tr>
                <td class="center">-</td>
                <td colspan="4">Aucun article</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- TOTALS -->
    <div class="totals-wrap">
        <table class="totals">
            <tr>
                <td>Montant HT</td>
                <td class="val">{{ number_format($totalAmount, 2, '.', '') }} DA</td>
            </tr>
            <tr>
                <td>TVA 19%</td>
                <td class="val">{{ number_format($totalAmount * 0.19, 2, '.', '') }} DA</td>
            </tr>
            <tr class="bold">
                <td><strong>Montant TTC</strong></td>
                <td class="val"><strong>{{ number_format($totalAmount * 1.19, 2, '.', '') }} DA</strong></td>
            </tr>
        </table>
        <div class="clear"></div>
    </div>

    <!-- AMOUNT IN WORDS -->
    <div class="amount-words">
        Arrêtée la présente facture a la somme de : {{ $total_in_words }} Dinar(s)
    </div>

    <!-- SIGNATURE -->
    <div class="signature">
        Cachet et Signature
    </div>

</body>
</html>
