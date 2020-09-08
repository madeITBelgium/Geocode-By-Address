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
    protected $version = '1.2.0';
    private $type = '';
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
            'timeout' => 10.0,
            'headers' => [
                'User-Agent' => 'madeITBelgium/Geocode-By-Address PHP SDK V'.$this->version,
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
        if (in_array($type, ['geocode.xyz', 'google', 'tomtom', 'openstreetmap'])) {
            $this->type = $type;
        } else {
            throw new \Exception('Wrong GEO Data type. Take one of: geocode.xyz, google, tomtom, openstreetmap');
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

    public function structuredLookup($streetName, $streetNumber, $municipality, $postalCode, $country)
    {
        if ($this->type === 'tomtom') {
            return $this->structuredLookupTomTom($streetName, $streetNumber, $municipality, $postalCode, $country);
        } elseif ($this->type === 'geocode.xyz') {
            return $this->lookupGeocodeXYZ($streetName.' '.$streetNumber.', '.$postalCode.' '.$municipality.', '.$country);
        } elseif ($this->type === 'google') {
            return $this->lookupGoogle($streetName.' '.$streetNumber.', '.$postalCode.' '.$municipality.', '.$country);
        } elseif ($this->type === 'openstreetmap') {
            return $this->lookupOpenstreetmap($streetName.' '.$streetNumber.', '.$postalCode.' '.$municipality.', '.$country);
        }

        throw new Exception($this->type.' do not support structured lookup');
    }

    public function lookup($address)
    {
        if ($this->type === 'geocode.xyz') {
            return $this->lookupGeocodeXYZ($address);
        } elseif ($this->type === 'google') {
            return $this->lookupGoogle($address);
        } elseif ($this->type === 'tomtom') {
            return $this->lookupTomTom($address);
        } elseif ($this->type === 'openstreetmap') {
            return $this->lookupOpenstreetmap($address);
        }

        throw new Exception($this->type.' do not support normal lookup');
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

    private function lookupTomTom($address)
    {
        $url = 'https://api.tomtom.com/search/2/geocode/';
        $url .= urlencode($address).'.json';
        if ($this->key !== null) {
            $url .= '?key='.$this->key;
        }

        $result = $this->getCall($url);
        $respons = json_decode($result, true);
        if (isset($respons['results'][0])) {
            $results = $respons['results'][0];
            if (isset($results['position']['lat'])) {
                return [
                    $results['position']['lat'],
                    $results['position']['lng'],
                ];
            }
        }

        return false;
    }

    private function structuredLookupTomTom($streetName, $streetNumber, $municipality, $postalCode, $country)
    {
        $data = [];
        if ($this->key !== null) {
            $data['key'] = $this->key;
        }

        if (strlen($country) > 3) {
            $data['countryCode'] = substr($country, 0, 2);
        } else {
            $data['countryCode'] = $country;
        }

        $data['streetNumber'] = $streetNumber;
        $data['streetName'] = $streetName;
        $data['municipality'] = $municipality;
        $data['postalCode'] = $postalCode;

        $url = 'https://api.tomtom.com/search/2/structuredGeocode.json?'.http_build_query($data);

        $result = $this->getCall($url);
        $respons = json_decode($result, true);
        if (isset($respons['results'][0])) {
            $results = $respons['results'][0];
            if (isset($results['position']['lat'])) {
                return [
                    $results['position']['lat'],
                    $results['position']['lng'],
                ];
            }
        }

        return false;
    }
    

    private function lookupOpenstreetmap($address)
    {
        $qry = http_build_query([
            'format' => 'jsonv2',
            'q' => $address,
            'addressdetails' => 1,
        ]);
        $url = 'https://nominatim.openstreetmap.org/search?' . $qry;
        
        $result = $this->getCall($url);
        $response = json_decode($result, true);
        if (isset($response[0]['lon']) && isset($response[0]['lat'])) {
            return [
                $response[0]['lat'],
                $response[0]['lon'],
            ];
        }

        return false;
    }
}
