<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Pointage des employés</title>
    <style>
        @page {
            margin: 24px 28px;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #1f2937;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .page {
            width: 100%;
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 12px;
        }

        .company,
        .meta {
            display: table-cell;
            vertical-align: top;
        }

        .company {
            width: 68%;
            padding-right: 12px;
        }

        .meta {
            width: 32%;
            text-align: right;
        }

        .company-name {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.2px;
            color: #0f172a;
        }

        .company-line {
            margin: 3px 0 0;
            font-size: 11.5px;
            color: #4b5563;
            line-height: 1.35;
        }

        .meta-label {
            margin: 0;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7280;
        }

        .meta-value {
            margin: 2px 0 0;
            font-size: 12px;
            font-weight: 700;
            color: #111827;
        }

        .report-title {
            margin: 8px 0 14px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            letter-spacing: 0.2px;
        }

        .divider {
            height: 1px;
            background: #dbe2ea;
            margin-bottom: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #d7dce3;
            padding: 7px 8px;
            vertical-align: top;
            word-wrap: break-word;
        }

        th {
            background: #f3f4f6;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #111827;
            text-align: left;
        }

        tbody tr:nth-child(even) td {
            background: #fafafa;
        }

        .col-mat {
            width: 7%;
            text-align: center;
        }

        .col-nom {
            width: 24%;
        }

        .col-prenom {
            width: 24%;
        }

        .col-jours {
            width: 9%;
            text-align: center;
        }

        .col-dates {
            width: 36%;
        }

        .dates {
            white-space: pre-line;
            line-height: 1.25;
        }

        .empty {
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>
<body>
    @php
        $monthNames = [
            1 => 'JANVIER',
            2 => 'FÉVRIER',
            3 => 'MARS',
            4 => 'AVRIL',
            5 => 'MAI',
            6 => 'JUIN',
            7 => 'JUILLET',
            8 => 'AOÛT',
            9 => 'SEPTEMBRE',
            10 => 'OCTOBRE',
            11 => 'NOVEMBRE',
            12 => 'DÉCEMBRE',
        ];
        $monthLabel = $monthNames[(int) $month] ?? strtoupper((string) $month);
        $companyName = $company?->name ?? 'SOCIÉTÉ';
        $companyAddress = trim((string) ($company?->address ?? ''));
        $companyDescription = trim((string) ($company?->description ?? ''));
    @endphp

    <div class="page">
        <div class="header">
            <div class="company">
                <p class="company-name">{{ $companyName }}</p>
                @if($companyAddress !== '')
                    <p class="company-line">{{ $companyAddress }}</p>
                @endif
                @if($companyDescription !== '')
                    <p class="company-line">{{ $companyDescription }}</p>
                @endif
            </div>
            <div class="meta">
                <p class="meta-label">Généré le</p>
                <p class="meta-value">{{ now()->format('d/m/Y H:i') }}</p>
                <p class="meta-label" style="margin-top: 10px;">Période</p>
                <p class="meta-value">MOIS : {{ $monthLabel }} / {{ str_pad((string) $month, 2, '0', STR_PAD_LEFT) }} / ANNÉE : {{ $year }}</p>
            </div>
        </div>

        <div class="report-title">POINTAGE DES EMPLOYÉS</div>
        <div class="divider"></div>

        <table>
            <thead>
                <tr>
                    <th class="col-mat">MAT</th>
                    <th class="col-nom">NOM</th>
                    <th class="col-prenom">PRÉNOM</th>
                    <th class="col-jours">JOURS</th>
                    <th class="col-dates">ENTRÉE / SORTIE</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $employee)
                    @php
                        $dateLines = [];
                        if (!empty($employee->out_date)) {
                            $dateLines[] = 'SORTIE : ' . $employee->out_date;
                        }
                        if (!empty($employee->in_date)) {
                            $dateLines[] = 'ENTRÉE : ' . $employee->in_date;
                        }
                        $entryExit = empty($dateLines) ? '-' : implode("\n", $dateLines);
                    @endphp
                    <tr>
                        <td class="col-mat">{{ $employee->id }}</td>
                        <td class="col-nom">{{ $employee->surname ?? '-' }}</td>
                        <td class="col-prenom">{{ $employee->name ?? '-' }}</td>
                        <td class="col-jours">{{ (int) ($employee->work_days ?? 0) }}</td>
                        <td class="col-dates">
                            <div class="dates">{{ $entryExit }}</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty">Aucun employé actif pour cette période.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
