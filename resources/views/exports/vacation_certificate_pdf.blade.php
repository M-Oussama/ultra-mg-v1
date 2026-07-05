<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>سند عطلة سنوية</title>
    @php
        $toFileUrl = static function (string $path): string {
            $normalized = str_replace('\\', '/', $path);
            if (preg_match('/^[A-Za-z]:\//', $normalized) === 1) {
                return 'file:///' . $normalized;
            }

            return 'file://' . $normalized;
        };

        $dejaVuRegular = $toFileUrl(base_path('vendor/dompdf/dompdf/lib/fonts/DejaVuSans.ttf'));
        $dejaVuBold = $toFileUrl(base_path('vendor/dompdf/dompdf/lib/fonts/DejaVuSans-Bold.ttf'));
    @endphp
    <style>
        @font-face {
            font-family: 'VacationArabic';
            src: url('{{ $dejaVuRegular }}') format('truetype');
            font-weight: 400;
            font-style: normal;
        }

        @font-face {
            font-family: 'VacationArabic';
            src: url('{{ $dejaVuBold }}') format('truetype');
            font-weight: 700;
            font-style: normal;
        }

        @page {
            size: A4;
            margin: 14mm 16mm 14mm 16mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            direction: rtl;
            font-family: 'VacationArabic', 'DejaVu Sans', Arial, sans-serif;
            color: #111111;
            font-size: 12px;
            line-height: 1.8;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .page {
            page-break-after: always;
            min-height: 260mm;
        }

        .page:last-child {
            page-break-after: auto;
        }

        .company-header {
            text-align: center;
            margin-bottom: 14px;
        }

        .company-name {
            font-size: 16px;
            font-weight: 700;
            text-decoration: underline;
            margin-bottom: 2px;
        }

        .company-line {
            font-size: 11.5px;
            font-weight: 700;
            margin-top: 2px;
        }

        .number-row {
            display: block;
            text-align: right;
            direction: rtl;
            unicode-bidi: plaintext;
            width: 100%;
            font-size: 12px;
            font-weight: 700;
            margin: 4px 0 18px;
        }

        .number-row-inner {
            display: inline-flex;
            flex-direction: row;
            align-items: center;
            white-space: nowrap;
            direction: ltr;
            gap: 4px;
        }

        .number-label {
            direction: rtl;
            unicode-bidi: isolate;
        }

        .number-value {
            direction: ltr;
            unicode-bidi: isolate;
            letter-spacing: 0.3px;
        }

        .number-separator {
            margin: 0 4px;
        }

        .title-box {
            margin: 0 24px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 11px 16px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
        }

        .content {
            margin-top: 28px;
            text-align: center;
            font-size: 15px;
            font-weight: 700;
            line-height: 2.1;
        }

        .content .line {
            margin-bottom: 8px;
        }

        .note-box {
            margin: 34px auto 0;
            width: 72%;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 8px 12px;
            text-align: center;
            font-size: 11px;
            font-weight: 700;
        }

        .signature {
            margin-top: 34px;
            text-align: right;
            font-size: 12px;
            font-weight: 700;
        }
    </style>
</head>
<body>
@php
    $variant = strtolower(trim((string) ($variant ?? 'signed')));
    if (!in_array($variant, ['signed', 'blank', 'both'], true)) {
        $variant = 'signed';
    }

    $pages = $variant === 'both'
        ? ['signed', 'blank']
        : [$variant === 'blank' ? 'blank' : 'signed'];

    $placeholder = static fn (int $count): string => str_repeat('.', $count);

    $formatDate = static function ($value, string $fallback = '.................'): string {
        if (empty($value)) {
            return $fallback;
        }

        try {
            return \Illuminate\Support\Carbon::parse($value)->format('d-m-Y');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    };

    $cleanValue = static function ($value, string $fallback): string {
        $text = trim((string) ($value ?? ''));
        return $text !== '' ? $text : $fallback;
    };

    $joinNonEmpty = static function (array $parts): string {
        $cleaned = [];

        foreach ($parts as $part) {
            $text = trim((string) ($part ?? ''));
            if ($text !== '') {
                $cleaned[] = $text;
            }
        }

        return implode(' ', $cleaned);
    };

    $companyName = $cleanValue(data_get($company, 'name'), 'EURL SETIFIS DETERGENTS');
    $companyAddress = $cleanValue(
        $joinNonEmpty([data_get($company, 'address'), data_get($company, 'address2')]),
        'LOTISSEMENT 34 SECT 6 GROUPE N° 51 KASR EL ABTAL'
    );
    $companyActivity = $cleanValue(
        data_get($company, 'description') ?? data_get($company, 'address2'),
        'FABRICATION DE PRODUITS DE BLANCHISEMENTS ET PRODUITS DE LA MAINT'
    );
    $companyEmployerNumber = $cleanValue(
        data_get($company, 'NIS') ?? data_get($company, 'nis') ?? data_get($company, 'NRC') ?? data_get($company, 'nrc'),
        '1960328646'
    );
    $companyFiscalNumber = $cleanValue(
        data_get($company, 'NIF') ?? data_get($company, 'nif') ?? data_get($company, 'NART') ?? data_get($company, 'nart'),
        '001319010024074'
    );

    $employeeArabicName = trim((string) data_get($employee, 'name_ar', '') . ' ' . (string) data_get($employee, 'surname_ar', ''));
    $employeeLatinName = trim((string) data_get($employee, 'name', '') . ' ' . (string) data_get($employee, 'surname', ''));
    $employeeDisplayName = $employeeArabicName !== '' ? $employeeArabicName : $employeeLatinName;
    $employeeDisplayName = $employeeDisplayName !== '' ? $employeeDisplayName : '.................';

    $position = trim((string) data_get($career, 'position_ar', ''));
    if ($position === '') {
        $position = trim((string) data_get($career, 'position', ''));
    }
    if ($position === '') {
        $position = '.................';
    }

    $vacationStart = data_get($vacation, 'start_date');
    $vacationEnd = data_get($vacation, 'end_date');
    $storedVacationYear = trim((string) (data_get($vacation, 'vacation_year') ?? ''));
    $vacationCount = (string) (data_get($vacation, 'count') ?? '');
    $yearRange = $vacationStart && $vacationEnd
        ? \Illuminate\Support\Carbon::parse($vacationStart)->year . '/' . \Illuminate\Support\Carbon::parse($vacationEnd)->year
        : '.................';
    $fromDate = $formatDate($vacationStart);
    $toDate = $formatDate($vacationEnd);
    $resumeDate = $vacationEnd
        ? \Illuminate\Support\Carbon::parse($vacationEnd)->addDay()->format('d-m-Y')
        : '.................';
@endphp

@foreach($pages as $pageType)
    <div class="page">
        @if($showCompanyInfo ?? true)
            <div class="company-header">
                <div class="company-name">{{ $companyName }}</div>
                <div class="company-line">Adresse : {{ $companyAddress }}</div>
                <div class="company-line">Activité : {{ $companyActivity }}</div>
                <div class="company-line">Numéro employeur : {{ $companyEmployerNumber }} &nbsp;&nbsp; M.F. : {{ $companyFiscalNumber }}</div>
            </div>
        @endif

        <div class="number-row">
            <div class="number-row-inner">
                <span class="number-label">رقم:</span>
                <span class="number-value">{{ $pageType === 'blank' ? $placeholder(16) : $certificateNumber }}</span>
            </div>
        </div>

        <div class="title-box">سند عطلة سنوية</div>

        <div class="content">
            <div class="line">اللقب و الاسم : {{ $pageType === 'blank' ? $placeholder(26) : $employeeDisplayName }}</div>
            <div class="line">الوظيفة : {{ $pageType === 'blank' ? $placeholder(30) : $position }}</div>
            <div class="line">يستفيد من عطلة : {{ $pageType === 'blank' ? $placeholder(16) : ($storedVacationYear !== '' ? $storedVacationYear : $yearRange) }}</div>
            <div class="line">عدد الأيام : {{ $pageType === 'blank' ? $placeholder(20) : ($vacationCount !== '' ? $vacationCount : $placeholder(20)) }}</div>
            <div class="line">من : {{ $pageType === 'blank' ? $placeholder(20) : $fromDate }} إلى : {{ $pageType === 'blank' ? $placeholder(20) : $toDate }} مدرج</div>
            <div class="line">يستأنف عمله يوم : {{ $pageType === 'blank' ? $placeholder(20) : $resumeDate }}</div>
        </div>

        <div class="note-box">يستفيد بهذا السند لاستعماله في الإطار المسموح به شرعاً.</div>

        <div class="signature">توقيع المعني : ................................</div>
    </div>
@endforeach
</body>
</html>
