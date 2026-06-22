<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>عقد عمل محدد المدة</title>
    <style>
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
            direction: rtl;
            font-family: Tahoma, "Segoe UI", Arial, "DejaVu Sans", sans-serif;
            color: #111111;
            font-size: 12px;
            line-height: 1.8;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .page {
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
        }

        .company-header {
            text-align: center;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .company-line {
            font-size: 11.5px;
            margin-top: 2px;
        }

        .top-number {
            text-align: right;
            font-size: 12px;
            margin: 6px 0 10px;
        }

        .contract-title {
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            margin: 8px 0 14px;
        }

        p {
            margin: 0 0 8px;
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .article {
            margin: 0 0 8px;
            text-align: right;
        }

        .article-title {
            font-weight: 700;
            text-decoration: underline;
        }

        .blank {
            display: inline-block;
            min-width: 160px;
            height: 14px;
            border-bottom: 1px solid #111111;
            vertical-align: baseline;
        }

        .blank.long {
            min-width: 280px;
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
            font-size: 16px;
            font-weight: 700;
            margin: 10px 0 10px;
        }

        .note {
            font-size: 11px;
        }

        .ltr-page {
            direction: ltr;
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
    </style>
</head>
<body>
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

<div class="page">
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

    <p>شركة <strong>{{ $companyName }}</strong> الكائن مقرها قطعة رقم 34 تجزئة 06 مجموعة رقم 51 قصر الأبطال، عين ولمان، سطيف</p>
    <p><u>من جهة</u></p>
    <p>و {{ $genderedBirthLabel }} : <strong>{{ $employeeArabicName }}</strong>، {{ $genderedBirthLabel }} بتاريخ: {{ $birthdate }} بـ: {{ trim((string) ($employee->birthplace ?? '.................')) }}, {{ $birthCityArabic }}</p>
    <p>حامل لبطاقة التعريف الوطنية رقم : {{ $employeeNcN }} ورقم التعريف الوطني : {{ $employeeNin }} المسلمة بتاريخ: {{ $cardIssueDate }}</p>
    <p><u>من جهة أخرى</u></p>
    <p><u>و تم الاتفاق على ما يلي:</u></p>

    <div class="article"><span class="article-title">المادة 01:</span> تشغل <strong>{{ $companyName }}</strong> السيد: {{ $employeeArabicName }} بصفته: <span class="blank">&nbsp;</span></div>
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
    <p>يصرح المسمى <strong>{{ $employeeArabicName }}</strong>، {{ $genderedBirthLabel }} بتاريخ: {{ $birthdate }}، بـ: {{ trim((string) ($employee->birthplace ?? '.................')) }}, {{ $birthCityArabic }}.</p>
    <p>إبن: {{ $fatherNameArabic }}. و: {{ $motherNameArabic }}.</p>
    <p>الحامل لبطاقة التعريف الوطنية رقم: {{ $employeeNcN }} ورقم التعريف الوطني: {{ $employeeNin }} الصادرة بتاريخ: {{ $cardIssueDate }}، عن {{ trim((string) ($employee->card_issue_place ?? '.................')) ?: '.................' }}، الولاية: {{ $issueCityArabic }}.</p>
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
    <p><strong>رقم البطاقة الوطنية:</strong> {{ $employeeNcN }}</p>
    <p><strong>رقم التعريف الوطني:</strong> {{ $employeeNin }}</p>
    <p><strong>الموضوع :</strong> الاستقالة</p>

    <p>سيدي المدير،</p>

    <p>أنا الموظف: {{ $employeeArabicName ?: $employeeLatinName }}، حامل لبطاقة الهوية الوطنية رقم {{ $employeeNcN }} ورقم التعريف الوطني {{ $employeeNin }}، أعلمكم بقراري بالاستقالة من منصبي في شركة <strong>{{ $companyName }}</strong>. اعتباراً من <strong>............................</strong>.</p>

    <p style="text-align: left; margin-top: 18px;">سطيف في :..............</p>
    <p style="text-align: right; margin-top: 6px;"><strong>التوقيع</strong></p>
</div>
</body>
</html>
