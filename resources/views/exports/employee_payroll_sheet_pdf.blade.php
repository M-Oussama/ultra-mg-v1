<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Payroll Sheet</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #1f2937; font-size: 10px; }
        .header { margin-bottom: 14px; }
        .title { font-size: 18px; font-weight: bold; margin: 0; }
        .meta { color: #6b7280; font-size: 10px; margin-top: 4px; }
        .summary { width: 100%; border-collapse: collapse; margin: 12px 0 14px; }
        .summary td { border: 1px solid #e5e7eb; padding: 7px 9px; }
        .summary .label { color: #6b7280; font-size: 9px; }
        .summary .value { font-size: 12px; font-weight: bold; margin-top: 2px; }
        table.ledger { width: 100%; border-collapse: collapse; }
        .ledger th, .ledger td { border: 1px solid #e5e7eb; padding: 6px 7px; text-align: left; vertical-align: middle; }
        .ledger th { background: #f3f4f6; font-weight: bold; font-size: 9px; }
        .amount { text-align: right; white-space: nowrap; }
        .line { height: 22px; }
        .empty { text-align: center; color: #6b7280; padding: 18px; }
    </style>
</head>
<body>
    <div class="header">
        <p class="title">{{ $company->name ?? 'Company' }} - Employee Payroll Sheet</p>
        @if (!empty($company?->address))
            <p class="meta">{{ $company->address }}</p>
        @endif
        <p class="meta">Month: {{ str_pad((string) $month, 2, '0', STR_PAD_LEFT) }} / Year: {{ $year }}</p>
    </div>

    <table class="summary">
        <tr>
            <td>
                <div class="label">Employees</div>
                <div class="value">{{ $employees->count() }}</div>
            </td>
            <td>
                <div class="label">Gross salary</div>
                <div class="value">{{ number_format((float) ($totals['gross_salary'] ?? 0), 2, '.', ',') }}</div>
            </td>
            <td>
                <div class="label">Objectives</div>
                <div class="value">{{ number_format((float) ($totals['objectives'] ?? 0), 2, '.', ',') }}</div>
            </td>
            <td>
                <div class="label">Total payable</div>
                <div class="value">{{ number_format((float) ($totals['total_payable'] ?? 0), 2, '.', ',') }}</div>
            </td>
        </tr>
    </table>

    <table class="ledger">
        <thead>
            <tr>
                <th style="width: 32px;">#</th>
                <th>Employee</th>
                <th style="width: 80px;">Work Days</th>
                <th style="width: 95px;">Monthly Salary</th>
                <th style="width: 95px;">Salary Part</th>
                <th style="width: 95px;">Objectives</th>
                <th style="width: 95px;">Total</th>
                <th style="width: 100px;">Signature</th>
                <th style="width: 100px;">Fingerprint</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($employees as $index => $employee)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ trim($employee->name . ' ' . ($employee->surname ?? '')) }}</td>
                    <td>{{ (int) ($employee->work_days ?? 0) }}</td>
                    <td class="amount">{{ number_format((float) ($employee->monthly_salary ?? 0), 2, '.', ',') }}</td>
                    <td class="amount">{{ number_format((float) ($employee->salary_part ?? 0), 2, '.', ',') }}</td>
                    <td class="amount">{{ number_format((float) ($employee->objectives_amount ?? 0), 2, '.', ',') }}</td>
                    <td class="amount">{{ number_format((float) ($employee->total_payable ?? 0), 2, '.', ',') }}</td>
                    <td class="line"></td>
                    <td class="line"></td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="empty">No payroll employees found for this period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
