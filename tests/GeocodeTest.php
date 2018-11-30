<?php

use GuzzleHttp\Client;
use MadeITBelgium\Geocode\Geocode;
use PHPUnit\Framework\TestCase;

class GeocodeTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testConstructor()
    {
        $client = new Client([
            'base_uri' => 'http://localhost',
            'timeout'  => 5.0,
            'headers'  => [
                'Accept'     => 'application/json',
            ],
            'verify' => true,
        ]);
        $geocode = new Geocode($client);
    }
}
