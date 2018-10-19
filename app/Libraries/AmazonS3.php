<?php

namespace Vanguard\Libraries;

use \Aws\S3\S3Client;

Class AmazonS3
{
    const ENV_REGION = 'AWS_REGION';

    public static function generatePreSignedUrl()
    {
        $s3Client = S3Client::factory(array(
            'region' => getenv(self::ENV_REGION),
            'version' => '2006-03-01'
        ));

        $cmd = $s3Client->getCommand('PutObject', [
            'Bucket' => 'faya-dev-us-east-1-media',
            'Key'    => 'test-file.jpg',
        ]);
        $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');
        $presignedUrlPut = strval($request->getUri());
        return $presignedUrlPut;

    }
}
