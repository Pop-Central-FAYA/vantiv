<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function ajaxPost($uri, array $data = [])
    {
        \Session::start();
        return $this->post($uri, array_merge($data, ['_token' => \Session::token()]));
    }
}
