<?php

namespace Vanguard\Libraries\MpsImporter;
use Aws\S3\S3Client;
use Log;

class MpsFile
{

    const READ_BATCH = 2000000;

    protected $bucket = '';
    protected $key = '';
    protected $csv_location = '';
    protected $tmp_compressed_loc = '';

    public function __construct($bucket, $key)
    {
        $this->bucket = $bucket;
        $this->key = $key;
    }

    public function download()
    {
        return $this;
        // $this->tmp_compressed_loc = $this->downloadFromS3();
        // $this->csv_location = $this->uncompress($this->tmp_compressed_loc);
        // return $this;
    }

    public function getCsvFileName()
    {
        $csv_file = "/var/www/diary.csv";
        return $csv_file;
        // return $this->csv_location;
    }

    public function cleanup()
    {
        unlink($this->csv_location);
        unlink($this->tmp_compressed_loc);
        Log::info("Cleaned up the files");
    }

    /**
     * Download the compressed file to a temporary location from s3
     */
    protected function downloadFromS3()
    {
        $file_path = tempnam(sys_get_temp_dir(), 'tv-diary');
        Log::info("About to download {$this->bucket}/{$this->key} to {$file_path}");
        // $s3 = new S3Client(['version' => '2006-03-01', 'region' => 'us-east-1']);
        $s3 = new S3Client(['version' => 'latest', 'region' => 'us-east-1']);

        $result = $s3->getObject(['Bucket' => $this->bucket, 'Key'    => $this->key, 'SaveAs' => $file_path]);
        Log::info("Successfully downloaded {$this->bucket}/{$this->key} to {$file_path}");
        return $file_path;
    }

    protected function uncompress($tmp_compressed_loc)
    {
        $csv_location = tempnam(sys_get_temp_dir(), 'tv-diary');
        Log::info("About to uncompress {$tmp_compressed_loc} to {$csv_location}");

        $gzfh = gzopen($tmp_compressed_loc, "rb");
        $fh = fopen($csv_location, "w");

        while (!gzeof($gzfh)) {
            $string = gzread($gzfh, static::READ_BATCH);
            fwrite($fh, $string, strlen($string));
        }
        gzclose($gzfh);
        fclose($fh);
        Log::info("Successfully uncompressed {$tmp_compressed_loc} to {$csv_location}");
        return $csv_location;
    }    
}
