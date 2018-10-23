<?php

namespace Vanguard\Libraries;

use \Aws\S3\S3Client;

Class AmazonS3
{
    const ENV_REGION = 'AWS_REGION';
    const EXPIRATION = '+60 minutes';

    public static function generatePreSignedUrl($filename, $folder)
    {
        $filename = uniqid().'-'.$filename;
        $s3Client = S3Client::factory(array(
            'region' => getenv(self::ENV_REGION),
            'version' => '2006-03-01'
        ));

        if(\App::environment(['dev', 'local'])){
            $bucket = getenv('AWS_DEV_BUCKET_NAME');
        }else{
            $bucket = getenv('AWS_PROD_BUCKET_NAME');
        }

        $cmd = $s3Client->getCommand('PutObject', [
            'Bucket' => $bucket,
            'Key'    => $folder.$filename,
        ]);
        $request = $s3Client->createPresignedRequest($cmd, self::EXPIRATION);
        $presignedUrlPut = strval($request->getUri());
        return $presignedUrlPut;

    }
}
