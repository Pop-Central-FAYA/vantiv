<?php

use Tests\TestCase;

Class LoginTest extends TestCase {
    public function test_login_page()
    {
        $response = $this->call('GET', '/login');
        var_dump($response->getContent());
    }
}