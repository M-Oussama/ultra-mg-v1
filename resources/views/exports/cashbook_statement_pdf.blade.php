<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cashbook Statement</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #1f2937;
            margin: 0;
            padding: 28px;
            line-height: 1.45;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 18px;
        }
        .header-left,
        .header-right {
            display: table-cell;
            vertical-align: top;
        }
        .header-right {
            text-align: right;
        }
        .title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .subtitle {
            color: #6b7280;
        }
        .cashbook-name {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .muted {
            color: #6b7280;
        }
        .summary-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            margin: 14px 0 16px;
        }
        .summary-grid td {
            width: 25%;
            padding-right: 10px;
            vertical-align: top;
        }
        .summary-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 10px 12px;
            min-height: 72px;
        }
        .summary-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #6b7280;
            margin-bottom: 6px;
        }
        .summary-value {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
        }
        .summary-value.balance-positive {
            color: #166534;
        }
        .summary-value.balance-negative {
            color: #b91c1c;
        }
        .section-title {
            font-size: 14px;
            font-weight: 700;
            margin: 18px 0 8px;
            color: #111827;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead th {
            background: #111827;
            color: #fff;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 9px 8px;
            text-align: left;
        }
        tbody td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .num {
            text-align: right;
            white-space: nowrap;
        }
        .center {
            text-align: center;
        }
        .pill {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 999px;
            background: #f3f4f6;
            color: #374151;
            font-size: 10px;
            margin: 0 4px 4px 0;
            white-space: nowrap;
        }
        .attachment-box {
            margin-top: 4px;
            font-size: 10px;
            color: #4b5563;
        }
    </style>
</head>
<body>
    @php
        $cashbookName = data_get($cashbook, 'name', 'Cashbook');
        $cashbookDescription = data_get($cashbook, 'description', '');
        $summary = $summary ?? [];
        $transactions = collect($transactions ?? []);
        $attachments = $transactions->filter(fn ($transaction) => !empty(data_get($transaction, 'attachments', [])));
        $lastActivity = data_get($summary, 'last_activity');
        $balance = (float) data_get($summary, 'balance', data_get($cashbook, 'balance', 0));
        $balanceClass = $balance >= 0 ? 'balance-positive' : 'balance-negative';
        $contacts = collect(data_get($lookups, 'contacts', []));
        $categories = collect(data_get($lookups, 'categories', []));
        $paymentModes = collect(data_get($lookups, 'payment_modes', []));
    @endphp

    <div class="header">
        <div class="header-left">
            <div class="cashbook-name">{{ $cashbookName }}</div>
            <div class="muted">
                {{ $cashbookDescription !== '' ? $cashbookDescription : 'Standalone cashbook statement' }}
            </div>
        </div>
        <div class="header-right">
            <div class="title">Cashbook Statement</div>
            <div class="subtitle">
                @if($lastActivity)
                    Through {{ \Carbon\Carbon::parse($lastActivity)->format('d M Y') }}
                @else
                    Full ledger export
                @endif
            </div>
        </div>
    </div>

    <table class="summary-grid">
        <tr>
            <td>
                <div class="summary-card">
                    <div class="summary-label">Total Income</div>
                    <div class="summary-value">{{ number_format((float) data_get($summary, 'total_income', 0), 2, '.', ',') }}</div>
                </div>
            </td>
            <td>
                <div class="summary-card">
                    <div class="summary-label">Total Expense</div>
                    <div class="summary-value">{{ number_format((float) data_get($summary, 'total_expense', 0), 2, '.', ',') }}</div>
                </div>
            </td>
            <td>
                <div class="summary-card">
                    <div class="summary-label">Net Balance</div>
                    <div class="summary-value {{ $balanceClass }}">{{ number_format($balance, 2, '.', ',') }}</div>
                </div>
            </td>
            <td>
                <div class="summary-card">
                    <div class="summary-label">Entries</div>
                    <div class="summary-value">{{ (int) data_get($summary, 'transaction_count', 0) }}</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">Lookup Labels</div>
    <div class="muted">
        <strong>Contacts:</strong>
        @forelse($contacts as $contact)
            <span class="pill">{{ data_get($contact, 'name') }}</span>
        @empty
            <span class="muted">None</span>
        @endforelse
    </div>
    <div class="muted" style="margin-top: 6px;">
        <strong>Categories:</strong>
        @forelse($categories as $category)
            <span class="pill">{{ data_get($category, 'name') }}</span>
        @empty
            <span class="muted">None</span>
        @endforelse
    </div>
    <div class="muted" style="margin-top: 6px;">
        <strong>Payment modes:</strong>
        @forelse($paymentModes as $mode)
            <span class="pill">{{ data_get($mode, 'name') }}</span>
        @empty
            <span class="muted">None</span>
        @endforelse
    </div>

    <div class="section-title">Transaction Ledger</div>
    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Date</th>
                <th style="width: 8%;">Time</th>
                <th style="width: 9%;">Type</th>
                <th style="width: 13%;">Contact</th>
                <th style="width: 13%;">Category</th>
                <th style="width: 13%;">Mode</th>
                <th style="width: 12%;">Entry By</th>
                <th style="width: 28%;">Note</th>
                <th style="width: 14%;" class="num">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ data_get($transaction, 'transaction_date', '') }}</td>
                    <td>{{ data_get($transaction, 'transaction_time', '') ?: '-' }}</td>
                    <td>{{ strtoupper((string) data_get($transaction, 'type', '')) }}</td>
                    <td>{{ data_get($transaction, 'contact_name') ?: '-' }}</td>
                    <td>{{ data_get($transaction, 'category_name') ?: '-' }}</td>
                    <td>{{ data_get($transaction, 'payment_mode_name') ?: '-' }}</td>
                    <td>{{ data_get($transaction, 'user_name') ?: '-' }}</td>
                    <td>
                        {{ data_get($transaction, 'note') ?: '-' }}
                        @if(!empty(data_get($transaction, 'attachments', [])))
                            <div class="attachment-box">
                                Attachments:
                                @foreach(data_get($transaction, 'attachments', []) as $attachment)
                                    {{ data_get($attachment, 'file_name') }}@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td class="num">{{ number_format((float) data_get($transaction, 'amount', 0), 2, '.', ',') }}</td>
                </tr>
            @empty
            <tr>
                <td colspan="9">No transactions found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($attachments->isNotEmpty())
        <div class="section-title">Transactions With Attachments</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 14%;">Date</th>
                    <th style="width: 20%;">Entry</th>
                    <th>Files</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attachments as $transaction)
                    <tr>
                        <td>{{ data_get($transaction, 'transaction_date', '') }}</td>
                        <td>{{ data_get($transaction, 'note') ?: strtoupper((string) data_get($transaction, 'type', '')) }}</td>
                        <td>
                            @foreach(data_get($transaction, 'attachments', []) as $attachment)
                                <div>{{ data_get($attachment, 'file_name') }} {{ data_get($attachment, 'mime_type') ? '(' . data_get($attachment, 'mime_type') . ')' : '' }}</div>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
