<?php

namespace Vanguard\Libraries;

use \Aws\S3\S3Client;

Class AmazonS3
{
    public static function generatePreSignedUrl()
    {

        $s3Client = new S3Client([
            'profile' => 'default',
            'region' => 'us-east-2',
            'version' => '2006-03-01',
        ]);

        $cmd = $s3Client->getCommand('PutObject', [
            'Bucket' => 'faya-dev-us-east-1-media',
            'Key'    => 'test-file.jpg',
        ]);
        $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');
        $presignedUrlPut = strval($request->getUri());
        return $presignedUrlPut;

    }
}
