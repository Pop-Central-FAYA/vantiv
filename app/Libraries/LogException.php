<?php

namespace Vanguard\Libraries;

class LogException
{
    const FILE_NAME = '/exception_logs/exceptionLogs.txt';

    public function add($error)
    {
        file_put_contents( self::FILENAME, $error, FILE_APPEND);
    }
}