<?php

namespace CristianPeter\LaravelDisposableContactGuard\Integrations\NumCheckR\Client;

use CristianPeter\LaravelDisposableContactGuard\Integrations\NumCheckR\Exceptions\ApiErrorException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class NumCheckrClient implements NumCheckrClientInterface
{
    private const int TIMEOUT = 3;
    private const int RETRY_TIMES = 3;
    private const int RETRY_SLEEP_MILLISECONDS = 100;
    private string $url;
    private string $apiKey;

    public function __construct() {
        $this->url = mb_trim(config('disposable-guard.phone.integrations.numcheckr.url'));
        $this->apiKey = config('disposable-guard.phone.integrations.numcheckr.api_key');
    }

    /**
     * @throws ApiErrorException
     * @throws ConnectionException
     */
    public function post(array $data): array
    {
       return $this->send('post', $this->url ,$data);
    }

    /**
     * @throws ApiErrorException
     * @throws ConnectionException
     */
    public function isDisposable(string $number): bool
    {
        return $this->send('post', $this->url , ['phone' => $number])['is_disposable'];
    }

    /**
     * @throws ApiErrorException
     * @throws ConnectionException
     */
    private function send(string $method, string $url, ?array $data): array
    {
        try {
            $response = $this->call($method, $url, $data);
        } catch (RequestException $e) {
            throw new ApiErrorException($e->getMessage(), $e->getCode(), $e);
        }

        return $response->json();
    }

    /**
     * @throws ConnectionException
     */
    private function call(string $method, string $url, ?array $data): Response
    {
        return match ($method) {
            'post'   => $this->makePendingRequest()->asJson()->post($url, $data),
        };
    }

    private function makePendingRequest(): PendingRequest
    {
        return Http::acceptJson()
            ->withToken($this->apiKey)
            ->timeout(self::TIMEOUT)
            ->retry(self::RETRY_TIMES, self::RETRY_SLEEP_MILLISECONDS);
    }
}
