<?php

namespace Vanguard\Libraries\MpsImporter;

class MpsFile
{

    const READ_BATCH = 2000000;

    protected $s3_file_location = '';
    protected $csv_location = '';

    public function __construct($s3_file_location)
    {
        $this->s3_file_location = $s3_file_location;
    }

    public function download()
    {
        $tmp_compressed_loc = $this->downloadFromS3();
        $this->csv_location = $this->uncompress($tmp_compressed_loc);
        return $this;
    }

    public function getCsvFileName()
    {
        return $this->csv_location;
    }

    /**
     * Download the compressed file to a temporary location from s3
     */
    protected function downloadFromS3()
    {
        return '/var/www/diary.tar.gz';
    }

    protected function uncompress($tmp_compressed_loc)
    {
        $csv_location = '/var/www/diary.csv';
        return $csv_location;

        $gzfh = gzopen($tmp_compressed_loc, "rb");
        $fh = fopen($csv_location, "w");

        while (!gzeof($gzfh)) {
            $string = gzread($gzfh, static::READ_BATCH);
            fwrite($fh, $string, strlen($string));
        }
        gzclose($gzfh);
        fclose($fh);
        return $csv_location;
    }    
}
