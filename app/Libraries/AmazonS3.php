<?php

namespace Vanguard\Libraries;

use \Aws\S3\S3Client;
use phpDocumentor\Reflection\Types\Self_;

Class AmazonS3
{
    const ENV_REGION = 'AWS_REGION';
    const EXPIRATION = '+60 minutes';
    const S3_BUCKET = 'MEDIA_BUCKET';

    public static function generatePreSignedUrl($filename, $folder)
    {
        $filename = uniqid().'-'.$filename;
        $s3Client = S3Client::factory(array(
            'region' => getenv(self::ENV_REGION),
            'version' => '2006-03-01'
        ));

        $cmd = $s3Client->getCommand('PutObject', [
            'Bucket' => getenv(self::S3_BUCKET),
            'Key'    => $folder.$filename,
        ]);
        $request = $s3Client->createPresignedRequest($cmd, self::EXPIRATION);
        $presignedUrlPut = strval($request->getUri());
        return $presignedUrlPut;

    }

    public static function uploadToS3FromPath($pathToFile, $slug)
    {
        $s3Client = S3Client::factory(array(
            'region' => getenv(self::ENV_REGION),
            'version' => '2006-03-01'
        ));

        $result = $s3Client->putObject(array(
            'Bucket'     => getenv(self::S3_BUCKET),
            'Key'        => $slug,
            'SourceFile' => $pathToFile,
        ));
        return $result['ObjectURL'];
    }

}
