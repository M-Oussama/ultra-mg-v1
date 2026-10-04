@php
    $dompdfArabic = (bool) ($dompdfArabic ?? false);
    $toFileUrl = static function (string $path): string {
        $normalized = str_replace('\\', '/', $path);
        if (preg_match('/^[A-Za-z]:\//', $normalized) === 1) {
            return 'file:///' . $normalized;
        }

        return 'file://' . $normalized;
    };
    $arabicRegularPath = resource_path('fonts/tahoma.ttf');
    $arabicBoldPath = resource_path('fonts/tahomabd.ttf');
    $arabicRegular = $dompdfArabic
        ? str_replace('\\', '/', $arabicRegularPath)
        : $toFileUrl($arabicRegularPath);
    $arabicBold = $dompdfArabic
        ? str_replace('\\', '/', $arabicBoldPath)
        : $toFileUrl($arabicBoldPath);
@endphp
<!DOCTYPE html>
<html lang="ar" dir="{{ $dompdfArabic ? 'ltr' : 'rtl' }}">
<head>
    <meta charset="UTF-8">
    <title>Employment Contract</title>
    <style>
        @font-face {
            font-family: 'ContractArabic';
            src: url('{{ $arabicRegular }}');
            font-weight: 400;
            font-style: normal;
        }

        @font-face {
            font-family: 'ContractArabic';
            src: url('{{ $arabicBold }}');
            font-weight: 700;
            font-style: normal;
        }

        @page {
            size: A4;
            margin: 14mm 16mm 12mm 16mm;
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
            /* Keep the document canvas LTR; apply RTL to the Arabic page
             * content only so Chromium does not shift the page box itself. */
            direction: ltr;
            /* Segoe UI has a cleaner Arabic face than the old Tahoma-first
             * stack and is embedded by Chromium when it prints the PDF. */
            font-family: 'ContractArabic', Tahoma, Arial, sans-serif;
            color: #111111;
            font-size: 14.5px;
            line-height: 1.9;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .page {
            width: 100%;
            max-width: 178mm;
            margin: 0 auto;
            direction: {{ $dompdfArabic ? 'ltr' : 'rtl' }};
            page-break-after: always;
            overflow: hidden;
        }

        .page:last-child {
            page-break-after: auto;
        }

        /* The employment contract is intended to be a single A4 sheet. Keep
         * its tighter rhythm local to this document so the receipt and
         * resignation forms below retain their existing spacing. */
        .contract-page {
            page-break-inside: avoid;
        }

        .contract-page .company-header {
            margin-bottom: 6px;
        }

        .contract-page .company-name {
            font-size: 16px;
        }

        .contract-page .company-line {
            font-size: 10px;
            margin-top: 1px;
        }

        .contract-page .top-number {
            margin: 3px 0 5px;
            font-size: 11px;
        }

        .contract-page .contract-title {
            font-size: 21px;
            margin: 4px 0 7px;
        }

        .contract-page p,
        .contract-page .article {
            font-size: 12px;
            line-height: 1.45;
        }

        .contract-page p {
            margin-bottom: 5px;
        }

        .contract-page .article {
            margin-bottom: 5px;
        }

        .contract-page .blank {
            height: 13px;
        }

        .contract-page .signature-row {
            margin-top: 9px;
        }

        .contract-page .signature-line {
            margin-top: 6px;
        }

        .contract-page .note {
            font-size: 11px;
        }

        .company-header {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .company-line {
            font-size: 12px;
            margin-top: 2px;
        }

        .top-number {
            text-align: right;
            font-size: 12px;
            margin: 6px 0 10px;
        }

        .contract-title {
            text-align: center;
            font-size: 26px;
            font-weight: 700;
            margin: 8px 0 14px;
        }

        p {
            margin: 0 0 10px;
            text-align: right;
            direction: rtl;
            unicode-bidi: plaintext;
            overflow-wrap: break-word;
            word-break: normal;
        }

        .center {
            text-align: center;
        }

        .latin {
            direction: ltr;
            unicode-bidi: isolate;
            white-space: nowrap;
        }

        .number {
            direction: ltr;
            unicode-bidi: isolate;
            white-space: nowrap;
        }

        .article {
            margin: 0 0 10px;
            text-align: right;
            direction: rtl;
            unicode-bidi: plaintext;
            line-height: 1.9;
            overflow-wrap: break-word;
            word-break: normal;
        }

        .article-title {
            font-weight: 700;
            text-decoration: underline;
        }

        .blank {
            display: inline-block;
            width: 160px;
            height: 16px;
            border-bottom: 0;
            vertical-align: baseline;
            white-space: nowrap;
            direction: ltr;
            unicode-bidi: isolate;
            font-family: Arial, sans-serif;
            font-size: 11px;
            letter-spacing: 1px;
            overflow: hidden;
        }

        .blank.long {
            width: 280px;
        }

        .blank::after {
            content: '................................';
        }

        .blank.long::after {
            content: '........................................................';
        }

        .signature-row {
            display: table;
            width: 100%;
            margin-top: 18px;
        }

        .signature-cell {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .signature-line {
            margin-top: 12px;
        }

        .section-title {
            text-align: center;
            font-size: 19px;
            font-weight: 700;
            margin: 10px 0 10px;
        }

        .note {
            font-size: 13px;
        }

        .ltr-page {
            direction: ltr;
            font-family: Arial, sans-serif;
        }

        .ltr-page p,
        .ltr-page .company-header,
        .ltr-page .top-number,
        .ltr-page .contract-title {
            direction: ltr;
            text-align: left;
        }

        .ltr-page .company-name,
        .ltr-page .section-title {
            text-align: center;
        }

        .small-gap {
            margin-top: 4px;
        }

        /* Dompdf receives visually shaped Arabic text from the controller.
         * Keep that fallback in visual left-to-right order so long lines wrap
         * inside the page instead of being clipped at the right edge. */
        .dompdf-fallback .page,
        .dompdf-fallback p,
        .dompdf-fallback .article {
            direction: ltr;
            text-align: left;
        }

        .dompdf-fallback .company-header,
        .dompdf-fallback .contract-title,
        .dompdf-fallback .section-title {
            direction: ltr;
        }

        .dompdf-fallback .contract-title,
        .dompdf-fallback .section-title,
        .dompdf-fallback .company-name {
            text-align: center;
        }

        .dompdf-fallback .top-number {
            text-align: left;
        }
    </style>
</head>
<body class="{{ $dompdfArabic ? 'dompdf-fallback' : '' }}">
@php
    $formatDate = static function ($value, string $format = 'd-m-Y'): string {
        if (empty($value)) {
            return '.................';
        }

        try {
            return \Illuminate\Support\Carbon::parse($value)->format($format);
        } catch (\Throwable $e) {
            return (string) $value;
        }
    };

    $companyName = trim((string) data_get($company, 'name', '')) ?: 'EURL SETIFIS DETERGENTS';
    $companyAddress = trim((string) data_get($company, 'address', '')) ?: 'LOTISSEMENT 34 SECT 6 GROUPE N° 51 KASR EL ABTAL';

    $companyActivity = trim((string) data_get($company, 'activity', ''));
    if ($companyActivity === '') {
        $companyActivity = trim((string) data_get($company, 'profession', ''));
    }
    if ($companyActivity === '') {
        $companyActivity = 'FABRICATION DE PRODUITS DE BLANCHISEMENTS ET PRODUITS DE LA MAINT';
    }

    $companyEmployerNumber = trim((string) data_get($company, 'employer_number', ''));
    if ($companyEmployerNumber === '') {
        $companyEmployerNumber = '1960328646';
    }

    $companyMf = trim((string) data_get($company, 'mf', ''));
    if ($companyMf === '') {
        $companyMf = '001319010024074';
    }

    $employeeLatinName = trim((string) ($employee->name ?? '') . ' ' . (string) ($employee->surname ?? ''));
    $employeeArabicName = trim((string) ($employee->name_ar ?? '') . ' ' . (string) ($employee->surname_ar ?? ''));
    if ($employeeArabicName === '') {
        $employeeArabicName = $employeeLatinName;
    }

    $fatherNameArabic = trim((string) ($employee->father_name_ar ?? '')) ?: '.................';
    $motherNameArabic = trim((string) ($employee->mother_full_name_ar ?? '')) ?: '.................';
    $birthCityArabic = trim((string) data_get($employee, 'birthCity.name_ar', ''));
    if ($birthCityArabic === '') {
        $birthCityArabic = trim((string) data_get($employee, 'birthCity.name', ''));
    }
    if ($birthCityArabic === '') {
        $birthCityArabic = trim((string) ($employee->birthplace ?? ''));
    }
    if ($birthCityArabic === '') {
        $birthCityArabic = '.................';
    }

    $issueCityArabic = trim((string) data_get($employee, 'cardIssuedCity.name_ar', ''));
    if ($issueCityArabic === '') {
        $issueCityArabic = trim((string) data_get($employee, 'cardIssuedCity.name', ''));
    }
    if ($issueCityArabic === '') {
        $issueCityArabic = trim((string) ($employee->card_issue_place ?? ''));
    }
    if ($issueCityArabic === '') {
        $issueCityArabic = '.................';
    }

    $birthdate = $formatDate($employee->birthdate ?? null);
    $cardIssueDate = $formatDate($employee->card_issue_date ?? null);
    $employeeNcN = trim((string) ($employee->NCN ?? '')) ?: '.................';
    $employeeNin = trim((string) ($employee->NIN ?? '')) ?: '.................';
    $position = trim((string) ($career->position_ar ?? $career->position ?? '')) ?: '...................';
    $startDate = $formatDate($career->start_date ?? null);
    $endDate = $formatDate($career->end_date ?? null);
    $realStartDate = $formatDate($career->real_start_date ?? null);
    $realEndDate = $formatDate($career->real_end_date ?? null);
    $genderedBirthLabel = trim((string) ($employee->gender ?? '')) === 'f' ? 'المولودة' : 'المولود(ة)';
@endphp

<div class="page contract-page">
    <div class="company-header">
        <div class="company-name"><u>{{ $companyName }}</u></div>
        <div class="company-line"><u>Adresse</u> : {{ $companyAddress }}</div>
        <div class="company-line"><u>Activité</u> : {{ $companyActivity }}</div>
        <div class="company-line"><u>Numéro employeur</u> : {{ $companyEmployerNumber }} &nbsp;&nbsp; <u>M.F.</u> : {{ $companyMf }}</div>
    </div>

    <div class="top-number">رقم : .................</div>

    <div class="contract-title">*** عقد عمل محدد المدة ***</div>

    <p>بناء على القانون 11/90 المؤرخ في 21 أفريل 1990 والمتعلق بعلاقات العمل المعدل والمتمم.</p>

    <p class="center"><u>تم الاتفاق بين:</u></p>

    <p>شركة <strong class="latin">{{ $companyName }}</strong> الكائن مقرها قطعة رقم 34 تجزئة 06 مجموعة رقم 51 قصر الأبطال، عين ولمان، سطيف</p>
    <p><u>من جهة</u></p>
    <p>و {{ $genderedBirthLabel }} : <strong>{{ $employeeArabicName }}</strong>، {{ $genderedBirthLabel }} بتاريخ: <span class="number">{{ $birthdate }}</span> بـ: {{ trim((string) ($employee->birthplace ?? '.................')) }}, {{ $birthCityArabic }}</p>
    <p>حامل لبطاقة التعريف الوطنية رقم : <span class="number">{{ $employeeNcN }}</span> ورقم التعريف الوطني : <span class="number">{{ $employeeNin }}</span> المسلمة بتاريخ: <span class="number">{{ $cardIssueDate }}</span></p>
    <p><u>من جهة أخرى</u></p>
    <p><u>و تم الاتفاق على ما يلي:</u></p>

    <div class="article"><span class="article-title">المادة 01:</span> تشغل <strong class="latin">{{ $companyName }}</strong> السيد: {{ $employeeArabicName }} بصفته: <span class="blank">&nbsp;</span></div>
    <div class="article"><span class="article-title">المادة 02:</span> يتقاضى على هذا الأساس مبلغا قاعديا: <span class="blank long">&nbsp;</span> دج</div>
    <div class="article"><span class="article-title">المادة 03:</span> يستفيد المعني من مزايا الضمان الاجتماعي، العطل والخدمات الاجتماعية.</div>
    <div class="article"><span class="article-title">المادة 04:</span> يسري هذا العقد من: <span class="blank">&nbsp;</span> إلى <span class="blank">&nbsp;</span></div>
    <div class="article"><span class="article-title">المادة 05:</span> يخضع المعني لفترة تجريبية مدتها <span class="blank long">&nbsp;</span></div>
    <div class="article"><span class="article-title">المادة 06:</span> خلال فترة التجربة وفترة تمديد التجربة يمكن قطع علاقة العمل في أي وقت من أحد الطرفين دون إشعار مسبق ودون تعويضات.</div>
    <div class="article"><span class="article-title">المادة 07:</span> إذا كانت فترة التجربة غير مرضية إما أن تمدد فترة التجربة إلى مدة مساوية أو تقطع علاقة العمل.</div>
    <div class="article"><span class="article-title">المادة 08:</span> تنتهي علاقة العمل بانتهاء المدة المتعاقد عليها.</div>
    <div class="article"><span class="article-title">المادة 09:</span> يمكن تجديد هذا العقد إذا تطلب ذلك ضرورة للمصلحة وبنفس الشكل لمدة يحددها الطرفان.</div>
    <div class="article"><span class="article-title">المادة 10:</span> يستفيد العامل بجميع الحقوق ويلزم بجميع الواجبات المحددة في التشريع والتنظيم المعمول بهما.</div>
    <div class="article"><span class="article-title">المادة 11:</span> الأمور غير الواضحة في هذا العقد يرجع فيها إلى النظام الداخلي الساري المفعول المطلع عليه من طرف العامل، كما يلزم احترام العقد بدقة.</div>
    <div class="article"><span class="article-title">المادة 12:</span> يتوجب على الموظفين إتمام فترة انتقالية بعد تقديمهم الاستقالة تحدد مدتها الشركة، وذلك لضمان استكمال المهام وتسليم المسؤوليات بشكل منظم وسلس.</div>
    <div class="article"><span class="article-title">المادة 13:</span> مكان العمل: عامل داخل مقر الشركة.</div>

    <div class="signature-row signature-box">
        <div class="signature-cell">
            <p class="signature-line">توقيع المعني: <span class="blank long">&nbsp;</span></p>
            <p class="note">مع كتابة الإسم واللقب</p>
        </div>
        <div class="signature-cell">
            <p class="signature-line">توقيع الرئيس المدير العام: <span class="blank long">&nbsp;</span></p>
        </div>
    </div>
</div>

<div class="page">
    <div class="company-header">
        <div class="company-name">{{ $companyName }}</div>
        <div class="company-line">{{ $companyAddress }}</div>
        <div class="company-line">{{ $companyActivity }}</div>
    </div>

    <div class="center" style="font-size: 16px; font-weight: 700; margin: 6px 0 10px;">الجمهورية الجزائرية الديمقراطية الشعبية</div>

    <div class="section-title">محضر إعتراف بتقاضي الحقوق والمستحقات</div>

    <p class="note">رقم العقد: <span class="blank">&nbsp;</span> &nbsp; المؤرخ في: <span class="blank">&nbsp;</span></p>
    <p>طبقا للمادة 10 من قانون العمل.</p>
    <p>يصرح المسمى <strong>{{ $employeeArabicName }}</strong>، {{ $genderedBirthLabel }} بتاريخ: <span class="number">{{ $birthdate }}</span>، بـ: {{ trim((string) ($employee->birthplace ?? '.................')) }}, {{ $birthCityArabic }}.</p>
    <p>إبن: {{ $fatherNameArabic }}. و: {{ $motherNameArabic }}.</p>
    <p>الحامل لبطاقة التعريف الوطنية رقم: <span class="number">{{ $employeeNcN }}</span> ورقم التعريف الوطني: <span class="number">{{ $employeeNin }}</span> الصادرة بتاريخ: <span class="number">{{ $cardIssueDate }}</span>، عن {{ trim((string) ($employee->card_issue_place ?? '.................')) ?: '.................' }}، الولاية: {{ $issueCityArabic }}.</p>
    <p>إنني إستلمت جميع حقوقي وأمضيتها بمحضر إرادتي ودون إكراه من أحد.</p>
    <p>حرر بسطيف يوم: <span class="blank long">&nbsp;</span></p>
    <p>إمضاء وبصمة المعني</p>
</div>

<div class="page ltr-page">
    <div class="company-header">
        <div class="company-name">{{ $companyName }}</div>
        <div class="company-line">Adresse : {{ $companyAddress }}</div>
        <div class="company-line">Activité : {{ $companyActivity }}</div>
        <div class="company-line">Numéro employeur : {{ $companyEmployerNumber }} &nbsp;&nbsp; M.F. : {{ $companyMf }}</div>
    </div>

    <p><strong>Nom :</strong> {{ $employee->surname ?? $employeeLatinName }}</p>
    <p><strong>Prénom :</strong> {{ $employee->name ?? $employeeLatinName }}</p>
    <p><strong>Numéro carte nationale :</strong> {{ $employeeNcN }}</p>
    <p><strong>Numéro d'identification nationale :</strong> {{ $employeeNin }}</p>
    <p><strong>Objet :</strong> Démission</p>

    <p>Madame, Monsieur,</p>

    <p>Par la présente, je, {{ $employeeLatinName ?: '................' }}, titulaire de la carte nationale d'identité numéro {{ $employeeNcN }} et d'identification nationale numéro {{ $employeeNin }}, informe de ma décision de démissionner de mon poste au sein de <strong>{{ $companyName }}</strong>. Ma démission sera effective dans a partir de <strong>............................</strong>.</p>

    <p style="text-align: right; margin-top: 20px;"><strong>[Signature]</strong></p>
</div>

<div class="page">
    <div class="company-header">
        <div class="company-name">{{ $companyName }}</div>
        <div class="company-line">العنوان : {{ $companyAddress }}</div>
        <div class="company-line">النشاط : {{ $companyActivity }}</div>
    </div>

    <p><strong>الاسم:</strong> {{ $employeeArabicName ?: $employeeLatinName }}</p>
    <p><strong>اللقب:</strong> {{ $employee->surname_ar ?? $employee->surname ?? '.................' }}</p>
    <p><strong>رقم البطاقة الوطنية:</strong> <span class="number">{{ $employeeNcN }}</span></p>
    <p><strong>رقم التعريف الوطني:</strong> <span class="number">{{ $employeeNin }}</span></p>
    <p><strong>الموضوع :</strong> الاستقالة</p>

    <p>سيدي المدير،</p>

    <p>أنا الموظف: {{ $employeeArabicName ?: $employeeLatinName }}، حامل لبطاقة الهوية الوطنية رقم <span class="number">{{ $employeeNcN }}</span> ورقم التعريف الوطني <span class="number">{{ $employeeNin }}</span>، أعلمكم بقراري بالاستقالة من منصبي في شركة <strong class="latin">{{ $companyName }}</strong>. اعتباراً من <strong>............................</strong>.</p>

    <p style="text-align: left; margin-top: 18px;">سطيف في :..............</p>
    <p style="text-align: right; margin-top: 6px;"><strong>التوقيع</strong></p>
</div>
</body>
</html>
