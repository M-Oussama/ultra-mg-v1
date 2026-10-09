<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('certify:prune-import-uploads', function () {
    $removed = app(\App\Services\CertifyBundleChunkUpload::class)->pruneExpired();
    $this->info('Removed '.$removed.' expired temporary import upload(s).');
})->purpose('Remove abandoned Certify ZIP fragments after their import status expires');
