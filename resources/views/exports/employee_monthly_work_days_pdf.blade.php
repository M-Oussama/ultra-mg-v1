<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Monthly Work Days</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #1f2937; font-size: 12px; }
        .header { margin-bottom: 20px; }
        .title { font-size: 20px; font-weight: bold; margin: 0; }
        .meta { color: #6b7280; font-size: 11px; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px 10px; text-align: left; }
        th { background: #f3f4f6; font-weight: bold; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 999px; background: #e5f7ee; color: #166534; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <p class="title">{{ $company->name ?? 'Company' }} - Employee Monthly Work Days</p>
        <p class="meta">Month: {{ str_pad((string) $month, 2, '0', STR_PAD_LEFT) }} / Year: {{ $year }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 54px;">#</th>
                <th>Employee</th>
                <th>Position</th>
                <th style="width: 120px;">Work Days</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($employees as $index => $employee)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ trim($employee->name . ' ' . ($employee->surname ?? '')) }}</td>
                    <td>{{ $employee->position ?? '-' }}</td>
                    <td><span class="badge">{{ (int) ($employee->work_days ?? 0) }} days</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center; color:#6b7280;">No active employees found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
