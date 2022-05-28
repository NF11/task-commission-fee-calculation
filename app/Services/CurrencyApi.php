<?php


namespace App\Services;


use App\Traits\ConsumesExternalServices;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;

class CurrencyApi
{
    use ConsumesExternalServices;

    protected string $baseUri;

    public function __construct()
    {
        $this->baseUri = config('params.currencyApi');
    }

    /**
     * @throws JsonException
     */
    public function decodeResponse($response)
    {
        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws GuzzleException|JsonException
     */
    public function getCurrencyExchangeRates(string $code): array
    {
        return $this->makeRequest(
            'GET',
            "currency-exchange-rates",
        )['rates'];
    }

}
