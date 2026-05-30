<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bon de Livraison {{ $sale->id }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #1e1e1e;
            background: #fff;
            padding: 40px 44px 36px;
        }

        /* ── HEADER ── */
        .hdr { display: table; width: 100%; margin-bottom: 36px; }
        .hdr-l { display: table-cell; vertical-align: top; width: 58%; }
        .hdr-r { display: table-cell; vertical-align: top; width: 42%; text-align: right; }

        .logo { max-width: 150px; max-height: 52px; display: block; margin-bottom: 8px; }
        .co-name { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
        .co-line { font-size: 10.5px; color: #666; line-height: 1.8; }

        .doc-type { font-size: 11px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: #999; margin-bottom: 4px; }
        .doc-num  { font-size: 20px; font-weight: 700; color: #1e1e1e; margin-bottom: 10px; }
        .doc-info { font-size: 11px; color: #666; line-height: 2; }
        .doc-info strong { color: #1e1e1e; font-weight: 500; }

        /* ── SEPARATOR ── */
        hr { border: none; border-top: 1.5px solid #f0f0f0; margin: 0 0 28px; }

        /* ── INFO CARDS ── */
        .info-grid { display: table; width: 100%; margin-bottom: 28px; }
        .info-card-l { display: table-cell; vertical-align: top; width: 50%; padding-right: 8px; }
        .info-card-r { display: table-cell; vertical-align: top; width: 50%; padding-left: 8px; }

        .info-card {
            border: 1px solid #ebebeb;
            border-radius: 8px;
            padding: 16px 18px;
        }
        .card-title {
            font-size: 9px; font-weight: 700;
            letter-spacing: 1.5px; text-transform: uppercase;
            color: #aaa; margin-bottom: 12px;
        }
        .card-row {
            display: table; width: 100%;
            padding: 7px 0;
            border-bottom: 1px solid #f5f5f5;
        }
        .card-row-last {
            display: table; width: 100%;
            padding: 7px 0;
        }
        .card-key { display: table-cell; font-size: 10.5px; color: #999; width: 45%; }
        .card-val { display: table-cell; font-size: 10.5px; font-weight: 700; color: #1e1e1e; text-align: right; }

        /* ── ITEMS TABLE ── */
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 28px; }
        table.items thead tr { border-bottom: 1.5px solid #e8e8e8; }
        table.items thead th {
            font-size: 9.5px; font-weight: 700;
            letter-spacing: 0.8px; text-transform: uppercase;
            color: #aaa; padding: 0 10px 10px; text-align: left;
        }
        table.items thead th.r { text-align: right; }
        table.items thead th.c { text-align: center; }
        table.items tbody tr { border-bottom: 1px solid #f4f4f4; }
        table.items tbody td { font-size: 11.5px; padding: 11px 10px; color: #1e1e1e; }
        table.items tbody td.r { text-align: right; }
        table.items tbody td.c { text-align: center; }
        table.items tbody td.muted { color: #ccc; font-size: 11px; }

        /* ── BOTTOM ── */
        .bottom { display: table; width: 100%; }
        .bot-l {
            display: table-cell; vertical-align: middle;
            width: 52%; padding-right: 28px; padding-top: 4px;
        }
        .bot-r { display: table-cell; vertical-align: top; width: 48%; }

        .amt-label { font-size: 9px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: #bbb; margin-bottom: 6px; }
        .amt-text  { font-size: 11px; color: #555; line-height: 1.7; font-style: italic; }
        .amt-text strong { font-style: normal; color: #1e1e1e; font-weight: 700; }

        .trow { display: table; width: 100%; padding: 9px 0; border-bottom: 1px solid #f4f4f4; }
        .tlbl { display: table-cell; font-size: 11px; color: #888; }
        .tval { display: table-cell; font-size: 11.5px; font-weight: 500; color: #1e1e1e; text-align: right; }

        .grand-row {
            display: table; width: 100%;
            margin-top: 10px;
            padding: 12px 16px;
            background: #f0faf4;
            border-radius: 8px;
            border: 1px solid #c3e8d0;
        }
        .grand-lbl { display: table-cell; font-size: 11.5px; font-weight: 700; color: #1e7a45; }
        .grand-val { display: table-cell; font-size: 14px; font-weight: 700; color: #1e7a45; text-align: right; }

        /* ── NOTE ── */
        .note-wrap { margin-top: 32px; padding-top: 18px; border-top: 1px solid #f0f0f0; }
        .note-text { font-size: 10.5px; color: #999; line-height: 1.7; }
        .note-text strong { color: #555; font-weight: 700; }
    </style>
</head>
<body>

    {{-- ── HEADER ── --}}
    <div class="hdr">
        <div class="hdr-l">
            @if(!empty($logoAbsolutePath))
                <img src="{{ $logoAbsolutePath }}" alt="Logo" class="logo">
            @endif
            <div class="co-name">{{ $departmentName }}</div>
            @if(!empty($departmentAddress))
                <div class="co-line">{{ $departmentAddress }}</div>
            @endif
            @if(!empty($departmentEmail))
                <div class="co-line">{{ $departmentEmail }}</div>
            @endif
            @if(!empty($departmentPhone))
                <div class="co-line">{{ $departmentPhone }}</div>
            @endif
        </div>
        <div class="hdr-r">
            <div class="doc-type">Bon de Livraison</div>
            <div class="doc-num">#{{ $sale->id }}</div>
            <div class="doc-info"><strong>{{ \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d') }}</strong></div>
            <div class="doc-info" style="margin-top:2px;">Paiement : <strong>{{ $sale->payment_method ?? 'Espèce' }}</strong></div>
        </div>
    </div>

    <hr>

    {{-- ── INFO CARDS ── --}}
    <div class="info-grid">
        <div class="info-card-l">
            <div class="info-card">
                <div class="card-title">Informations client</div>
                <div class="card-row">
                    <span class="card-key">Nom</span>
                    <span class="card-val">{{ trim(($sale->client->name ?? '') . ' ' . ($sale->client->surname ?? '')) }}</span>
                </div>
                <div class="card-row">
                    <span class="card-key">Adresse</span>
                    <span class="card-val">{{ $sale->client->address ?? '-' }}</span>
                </div>
                <div class="card-row">
                    <span class="card-key">Téléphone</span>
                    <span class="card-val">{{ $sale->client->phone ?? '-' }}</span>
                </div>
                <div class="card-row-last">
                    <span class="card-key">Email</span>
                    <span class="card-val">{{ $sale->client->email ?? '-' }}</span>
                </div>
            </div>
        </div>
        <div class="info-card-r">
            <div class="info-card">
                <div class="card-title">Référence commande</div>
                <div class="card-row">
                    <span class="card-key">Facture N°</span>
                    <span class="card-val">#{{ $sale->id }}</span>
                </div>
                <div class="card-row">
                    <span class="card-key">Mode de paiement</span>
                    <span class="card-val">{{ $sale->payment_method ?? 'Espèce' }}</span>
                </div>
                <div class="card-row-last">
                    <span class="card-key">Date</span>
                    <span class="card-val">{{ \Carbon\Carbon::parse($sale->sale_date)->format('Y-m-d') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── ITEMS TABLE ── --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:47%">Désignation</th>
                <th class="r" style="width:16%">Prix unit.</th>
                <th class="c" style="width:12%">Qté</th>
                <th class="r" style="width:20%">Montant</th>
            </tr>
        </thead>
        <tbody>
        @forelse($sale->saleItems as $index => $item)
            <tr>
                <td class="muted c">{{ $index + 1 }}</td>
                <td>{{ $item->product->name ?? '-' }}</td>
                <td class="r">{{ number_format((float) $item->price, 2, ',', ' ') }} DZD</td>
                <td class="c">{{ number_format((float) $item->quantity, 0, ',', ' ') }}</td>
                <td class="r">{{ number_format((float) $item->total_price, 2, ',', ' ') }} DZD</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center; padding:16px; color:#bbb;">Aucun article</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{-- ── BOTTOM ── --}}
    @php
        $sousTotal    = (float) $sale->total_amount;
        $paiement     = (float) ($sale->paid_amount ?? 0);
        $resteARegler = $sousTotal - $paiement;
    @endphp

    <div class="bottom">
        <div class="bot-l">
            <div class="amt-label">Arrêté en toutes lettres</div>
            <div class="amt-text"><strong>{{ $amountLetter }}</strong></div>
        </div>
        <div class="bot-r">
            <div class="trow">
                <span class="tlbl">Total</span>
                <span class="tval">{{ number_format($sousTotal, 2, ',', ' ') }} DZD</span>
            </div>
            <div class="trow">
                <span class="tlbl">Paiement reçu</span>
                <span class="tval">{{ number_format($paiement, 2, ',', ' ') }} DZD</span>
            </div>
            <div class="grand-row">
                <span class="grand-lbl">Reste à régler</span>
                <span class="grand-val">{{ number_format($resteARegler, 2, ',', ' ') }} DZD</span>
            </div>
        </div>
    </div>

    {{-- ── NOTE ── --}}
    <div class="note-wrap">
        <div class="note-text">
            <strong>Note :</strong> Merci pour votre règlement rapide. Nous apprécions votre collaboration.
        </div>
    </div>

</body>
</html>