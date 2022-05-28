<?php


namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;

trait ConsumesExternalServices
{
    /**
     * @param string $method
     * @param string $requestUrl
     * @param array $queryParams
     * @param array $fromParams
     * @param array $headers
     * @param false $isJsonRequest
     * @return array
     * @throws GuzzleException|JsonException
     */
    public function makeRequest(
        string $method,
        string $requestUrl,
        array  $queryParams = [],
        array  $fromParams = [],
        array  $headers = [],
        bool   $isJsonRequest = false
    ): array
    {
        $client = new Client([
            'base_uri' => $this->baseUri
        ]);

        if (method_exists($this, 'resolveAuthorization')) {
            $this->resolveAuthorization($queryParams, $fromParams, $headers);
        }

        $response = $client->request($method, $requestUrl, [
            $isJsonRequest ? 'json' : 'form_params' => $fromParams,
            'headers' => $headers,
            'query' => $queryParams,
        ]);

        $response = $response->getBody()->getContents();

        if (method_exists($this, 'decodeResponse')) {
            $response = $this->decodeResponse($response);
        }

        return $response;
    }
}
