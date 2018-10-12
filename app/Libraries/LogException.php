<?php

namespace Vanguard\Libraries;

class LogException
{
    const FILE_NAME = '/tmp/laravel.log';

    public function add($error)
    {
        file_put_contents( self::FILENAME, $error, FILE_APPEND);
    }
}