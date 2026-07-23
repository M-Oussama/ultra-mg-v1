<?php
$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';
$app = require $root . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$entries = App\Models\ImportationMoneyBalanceEntry::query()->orderBy('id')->get();
echo 'count=' . $entries->count() . PHP_EOL;
foreach ($entries as $e) {
    echo implode('|', [
        $e->id,
        $e->company_id,
        $e->year,
        $e->month,
        $e->group_title,
        $e->direction,
        $e->amount,
        $e->entry_date?->toDateString(),
        $e->sort_order,
    ]) . PHP_EOL;
}
