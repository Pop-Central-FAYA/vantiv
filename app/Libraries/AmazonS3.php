<?php

namespace Vanguard\Libraries;

use \Aws\S3\S3Client;
use Illuminate\Support\Str;

Class AmazonS3
{
    const ENV_REGION = 'AWS_REGION';
    const EXPIRATION = '+60 minutes';
    const S3_BUCKET = 'MEDIA_BUCKET';
    const READ_EXPIRATION = '24 hours';

    private static function createNewS3Client()
    {
        $s3Client = S3Client::factory(array(
            'region' => getenv(self::ENV_REGION),
            'version' => 'latest'
        ));

        return $s3Client;
    }

    public static function generatePreSignedUrl($filename, $folder)
    {
        $file_extension = Str::after($filename, '.');
        $filename = uniqid().'-'.uniqid().'.'.$file_extension;

        $s3Client = AmazonS3::createNewS3Client();

        $cmd = $s3Client->getCommand('PutObject', [
            'Bucket' => getenv(self::S3_BUCKET),
            'Key'    => $key = $folder.$filename,
        ]);
        $request = $s3Client->createPresignedRequest($cmd, self::EXPIRATION);
        $presignedUrlPut = strval($request->getUri());
        return $presignedUrlPut;

    }

    public static function getPresignedUrlToReadFile($key)
    {
        $s3Client = AmazonS3::createNewS3Client();

        $cmd = $s3Client->getCommand('GetObject', [
            'Bucket' => getenv(self::S3_BUCKET),
            'Key'    => $key,
        ]);
        $request = $s3Client->createPresignedRequest($cmd, self::READ_EXPIRATION);
        $presignedUrlPut = (string) $request->getUri();
        return $presignedUrlPut;
    }

    public static function uploadToS3FromPath($pathToFile, $slug)
    {
        $s3Client = AmazonS3::createNewS3Client();

        $result = $s3Client->putObject(array(
            'Bucket'     => getenv(self::S3_BUCKET),
            'Key'        => $slug,
            'SourceFile' => $pathToFile,
        ));
        return $result['ObjectURL'];
    }

}
