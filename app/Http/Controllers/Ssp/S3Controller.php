<?php

namespace Vanguard\Http\Controllers\Ssp;

use Vanguard\Http\Controllers\Controller;
use Vanguard\Libraries\AmazonS3;

class S3Controller extends Controller
{
    public function getPresignedUrl()
    {
        $presigned_url = AmazonS3::generatePreSignedUrl(request()->filename, request()->folder);
        return $presigned_url;
    }
}
