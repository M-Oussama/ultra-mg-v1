<?php

namespace App\Http\Controllers;

class MediaBundleUploadController extends BundleUploadController
{
    protected function importKind(): string
    {
        return 'certify_media';
    }
}
