<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee History</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color: #1f2937;
            margin: 0;
            padding: 24px;
            background: #fff;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 16px;
        }
        .header-left, .header-right {
            display: table-cell;
            vertical-align: top;
        }
        .header-right { text-align: right; }
        .company-name {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .muted {
            color: #6b7280;
            line-height: 1.45;
        }
        .title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .subtitle {
            color: #6b7280;
        }
        .panel {
            margin-top: 14px;
            padding: 12px 14px;
            background: #f9fafb;
            border-radius: 10px;
        }
        .summary-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
            margin-bottom: 18px;
        }
        .summary-grid td {
            width: 25%;
            vertical-align: top;
            padding: 0 8px 0 0;
        }
        .summary-card {
            background: #fff;
            border-radius: 10px;
            padding: 12px 14px;
            border: 1px solid #e5e7eb;
            min-height: 78px;
        }
        .summary-label {
            color: #6b7280;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 6px;
        }
        .summary-value {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
        }
        .summary-note {
            color: #6b7280;
            font-size: 9px;
            margin-top: 4px;
            line-height: 1.35;
        }
        h2 {
            font-size: 13px;
            margin: 18px 0 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead th {
            background: #111827;
            color: #fff;
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 7px 6px;
            text-align: left;
        }
        tbody td {
            padding: 7px 6px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .center { text-align: center; }
        .nowrap { white-space: nowrap; }
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 999px;
            background: #eef2ff;
            color: #3730a3;
            font-size: 9px;
            font-weight: 700;
        }
        .success {
            background: #ecfdf5;
            color: #047857;
        }
        .warning {
            background: #fffbeb;
            color: #b45309;
        }
        .danger {
            background: #fef2f2;
            color: #b91c1c;
        }
        .small {
            font-size: 9px;
            color: #6b7280;
        }
    </style>
</head>
<body>
@php
    $formatDate = static function ($value): string {
        if (empty($value)) {
            return '-';
        }

        try {
            return \Illuminate\Support\Carbon::parse($value)->format('d M Y');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    };

    $employeeName = trim(($employee->name ?? '') . ' ' . ($employee->surname ?? ''));
    $totalPeriods = count($periods ?? []);
    $totalVacations = count($vacations ?? []);
    $totals = $summary['totals'] ?? [];
    $currentGroup = $summary['current_group'] ?? null;
@endphp

<div class="header">
    <div class="header-left">
        @if($showCompanyInfo ?? true)
            <div class="company-name">{{ $company->name ?? 'Company' }}</div>
            <div class="muted">
                {{ $company->address ?? '' }}<br>
                {{ $company->phone ?? '' }}<br>
                {{ $company->email ?? '' }}
            </div>
        @endif
    </div>
    <div class="header-right">
        <div class="title">Employee History</div>
        <div class="subtitle">Periods and leave ledger</div>
    </div>
</div>

<div class="panel">
    <strong>{{ $employeeName !== '' ? $employeeName : 'Employee' }}</strong>
    <br>
    <span class="muted">
        {{ $employee->phone ?: '-' }} · {{ $employee->email ?: '-' }} · {{ $employee->birthCity?->name ?: '-' }}
    </span>
    <br>
    <span class="small">Generated on {{ $generatedAt->format('d M Y, H:i') }}</span>
</div>

<table class="summary-grid">
    <tr>
        <td>
            <div class="summary-card">
                <div class="summary-label">Periods</div>
                <div class="summary-value">{{ $totalPeriods }}</div>
                <div class="summary-note">Working periods recorded for this employee.</div>
            </div>
        </td>
        <td>
            <div class="summary-card">
                <div class="summary-label">Leave Entries</div>
                <div class="summary-value">{{ $totalVacations }}</div>
                <div class="summary-note">All vacation entries across every period.</div>
            </div>
        </td>
        <td>
            <div class="summary-card">
                <div class="summary-label">Accrued Days</div>
                <div class="summary-value">{{ number_format((float) ($totals['accrued_days'] ?? 0), 2) }}</div>
                <div class="summary-note">Based on 2.5 days per worked month.</div>
            </div>
        </td>
        <td>
            <div class="summary-card">
                <div class="summary-label">Balance</div>
                <div class="summary-value">{{ number_format((float) ($totals['balance_days'] ?? 0), 2) }}</div>
                <div class="summary-note">{{ $currentGroup ? 'Current block balance' : 'No active block' }}</div>
            </div>
        </td>
    </tr>
</table>

<h2>Employment Periods</h2>
<table>
    <thead>
        <tr>
            <th style="width:4%;">#</th>
            <th style="width:18%;">Position</th>
            <th style="width:10%;">Start</th>
            <th style="width:10%;">End</th>
            <th style="width:10%;">Real Start</th>
            <th style="width:10%;">Real End</th>
            <th style="width:10%;">Worked</th>
            <th style="width:10%;">Accrued</th>
            <th style="width:10%;">Used</th>
            <th style="width:8%;">Balance</th>
            <th style="width:10%;">Status</th>
        </tr>
    </thead>
    <tbody>
    @forelse($periods as $index => $period)
        <tr>
            <td class="center">{{ $index + 1 }}</td>
            <td><strong>{{ $period['position'] ?? '-' }}</strong></td>
            <td class="nowrap">{{ $formatDate($period['start_date'] ?? null) }}</td>
            <td class="nowrap">{{ $formatDate($period['end_date'] ?? null) }}</td>
            <td class="nowrap">{{ $formatDate($period['real_start_date'] ?? null) }}</td>
            <td class="nowrap">{{ $formatDate($period['real_end_date'] ?? null) }}</td>
            <td class="nowrap">{{ number_format((float) ($period['worked_days'] ?? 0), 0) }}</td>
            <td class="nowrap">{{ number_format((float) ($period['accrued_days'] ?? 0), 2) }}</td>
            <td class="nowrap">{{ number_format((float) ($period['used_days'] ?? 0), 2) }}</td>
            <td class="nowrap">{{ number_format((float) ($period['balance_days'] ?? 0), 2) }}</td>
            <td class="center">
                <span class="badge {{ !empty($period['is_closed']) ? 'warning' : 'success' }}">
                    {{ !empty($period['is_closed']) ? 'Closed' : 'Open' }}
                </span>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="11" class="center muted" style="padding:18px;">No employment periods found</td>
        </tr>
    @endforelse
    </tbody>
</table>

<h2>Leave Ledger</h2>
<table>
    <thead>
        <tr>
            <th style="width:5%;">#</th>
            <th style="width:14%;">Period</th>
            <th style="width:18%;">Position</th>
            <th style="width:16%;">Start</th>
            <th style="width:16%;">End</th>
            <th style="width:12%;">Days</th>
        </tr>
    </thead>
    <tbody>
    @forelse($vacations as $index => $vacation)
        <tr>
            <td class="center">{{ $index + 1 }}</td>
            <td class="nowrap">{{ $vacation['career_id'] ?? '-' }}</td>
            <td>{{ $vacation['position'] ?? '-' }}</td>
            <td class="nowrap">{{ $formatDate($vacation['start_date'] ?? null) }}</td>
            <td class="nowrap">{{ $formatDate($vacation['end_date'] ?? null) }}</td>
            <td class="nowrap">{{ (int) ($vacation['count'] ?? 0) }} day{{ ((int) ($vacation['count'] ?? 0)) === 1 ? '' : 's' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="center muted" style="padding:18px;">No leave entries found</td>
        </tr>
    @endforelse
    </tbody>
</table>

</body>
</html>
