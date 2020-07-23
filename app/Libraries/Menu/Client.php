<?php

namespace App\Libraries\Menu;

use GuzzleHttp\Client as GuzzleClient;

abstract class Client
{
    /**
     * @var GuzzleHttp\ClientInterface
     */
    private $http;

    public function http()
    {
        if (!$this->http) {
            $this->http = new GuzzleClient([
                'base_uri' => $this->baseUri(),
            ]);
        }

        return $this->http;
    }

    public function setHttp($http)
    {
        $this->http = $http;

        return $this;
    }

    /**
     * Make an http request.
     *
     * @param string $method
     * @param string $url
     * @param array $params
     * @param array $headers
     * @return mixed
     * @throws \Exception
     */
    public function request(string $method, string $url, array $params = [], array $headers = [])
    {
        $keyParams = strtoupper($method) == 'GET' ? 'query' : 'json';

        $response = $this->http()->request($method, $url, [
            $keyParams => $params,
            'debug' => false,
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode >= 300) {
            throw new \Exception("Failed to retrieve data. Code:" . $statusCode);
        }

        return json_decode((string) $response->getBody());
    }

    public function get(string $url, array $params = [], array $headers = [])
    {
        return $this->request('get', $url, $params, $headers);
    }

    public function post(string $url, array $params = [], array $headers = [])
    {
        return $this->request('post', $url, $params, $headers);
    }

    /**
     * Base url for client.
     *
     * @return string
     */
    abstract public function baseUri();
}
