<?php

namespace MadeITBelgium\Geocode;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

/**
 * Address Geo lookup Laravel PHP.
 *
 * @version    1.0.0
 *
 * @copyright  Copyright (c) 2018 Made I.T. (https://www.madeit.be)
 * @author     Tjebbe Lievens <tjebbe.lievens@madeit.be>
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-3.txt    LGPL
 */
class Geocode
{
    protected $version = '1.0.0';
    private $type = 'geocode.xyz';
    private $key = null;

    private $client;

    /**
     * Construct.
     *
     * @param $clientId
     * @param $clientSecret;
     * @param $client
     */
    public function __construct($type = 'geocode.xyz', $key = null, $client = null)
    {
        $this->setType($type);

        if ($client == null) {
            $this->createClient();
        } else {
            $this->client = $client;
        }
    }

    private function createClient()
    {
        $this->client = new Client([
            'timeout'  => 10.0,
            'headers'  => [
                'User-Agent' => 'Made I.T. PHP SDK V'.$this->version,
                'Accept'     => 'application/json',
            ],
            'verify' => true,
        ]);
    }

    public function setClient($client)
    {
        $this->client = $client;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setType($type)
    {
        if (in_array($type, ['geocode.xyz', 'google'])) {
            $this->type = $type;
        }
        else {
            throw new \Exception('Wrong GEO Data type. Take one of: geocode.xyz, google');
        }
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * Execute call.
     *
     * @param $requestType
     * @param $endPoint
     */
    private function call($requestType, $endPoint)
    {
        try {
            $response = $this->client->request($requestType, $endPoint);
        } catch (ServerException $e) {
            throw $e;
        } catch (ClientException $e) {
            throw $e;
        }

        if ($response->getStatusCode() == 200) {
            $body = (string) $response->getBody();
        } else {
            throw new Exception('Invalid statuscode');
        }

        return $body;
    }

    public function getCall($endPoint)
    {
        return $this->call('GET', $endPoint);
    }

    public function lookup($address)
    {
        if ($this->type === 'geocode.xyz') {
            return $this->lookupGeocodeXYZ($address);
        }

        return $this->lookupGoogle($address);
    }

    private function lookupGeocodeXYZ($address)
    {
        $url = 'https://geocode.xyz/'.urlencode($address).'?json=1';
        if ($this->key !== null) {
            $url .= '&key='.$this->key;
        }

        $result = $this->getCall($url);
        $respons = json_decode($result, true);
        if (isset($respons['longt'])) {
            return [
                $respons['latt'],
                $respons['longt'],
            ];
        }

        return false;
    }

    private function lookupGoogle($address)
    {
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=';
        $url .= urlencode($address);
        if ($this->key !== null) {
            $url .= '&key='.$this->key;
        }

        $result = $this->getCall($url);
        $respons = json_decode($result, true);
        if (isset($respons['results'][0])) {
            $results = $respons['results'][0];
            if (isset($results['geometry']['location']['lat'])) {
                return [
                    $results['geometry']['location']['lat'],
                    $results['geometry']['location']['lng'],
                ];
            }
        }

        return false;
    }
}
