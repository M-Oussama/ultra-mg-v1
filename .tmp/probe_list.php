<?php
$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';
$app = require $root . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = app(App\Http\Controllers\ImportationMoneyBalanceController::class);
$request = Illuminate\Http\Request::create('/api/importation-money-balance/list', 'GET', [
    'company_id' => 2,
    'year' => 2026,
    'month' => 7,
]);
$request->setUserResolver(function () {
    return App\Models\User::find(1);
});
$response = $controller->list($request);
echo $response->getContent() . PHP_EOL;
