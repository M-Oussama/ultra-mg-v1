<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Chèques</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #000; line-height: 1.4; }
        .header { margin-bottom: 30px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #eee; padding: 8px; border: 1px solid #000; text-align: center; }
        td { padding: 8px; border: 1px solid #000; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .cheque-card { border: 2px solid #000; padding: 15px; margin-bottom: 30px; page-break-inside: avoid; }
        .footer { position: fixed; bottom: 30px; width: 100%; text-align: center; font-size: 9px; border-top: 1px solid #000; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; font-size: 20px;">LISTE DES CHEQUES</h1>
        <p>Généré le: {{ date('d/m/Y H:i') }}</p>
    </div>

    @foreach($cheques as $cheque)
    <div class="cheque-card">
        <table style="border: none;">
            <tr>
                <td style="border: none; width: 60%;">
                    <h2 style="margin: 0;">Chèque №: {{ $cheque->cheque_number }}</h2>
                    <strong>Client:</strong> {{ $cheque->client->name }}<br>
                    <strong>Date:</strong> {{ date('d/m/Y', strtotime($cheque->cheque_date)) }}
                </td>
                <td style="border: none; width: 40%; text-align: right;">
                    <div style="font-size: 18px; font-weight: bold; border: 1px solid #000; padding: 10px; display: inline-block;">
                        {{ number_format($cheque->amount, 2) }} DA
                    </div>
                </td>
            </tr>
        </table>
        
        <div style="margin-top: 15px; font-style: italic;">
            Arrêté à la somme de : <strong>{{ $cheque->amountLetter }}</strong>
        </div>
    </div>
    @endforeach

    <div class="footer">
        {{ $company->name }} - {{ $company->address }}<br>
        {{ $company->email }}
    </div>
</body>
</html>
