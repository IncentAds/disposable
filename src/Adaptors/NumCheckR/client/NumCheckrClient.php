<?php

namespace CristianPeter\LaravelDisposableContactGuard\Adaptors\NumCheckR\client;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class NumCheckrClient implements NumCheckrClientInterface
{
    private const TIMEOUT = 3;
    private const RETRY_TIMES = 3;
    private const RETRY_SLEEP_MILLISECONDS = 100;

    public function __construct(private string $url, private string $apiKey) {
        $this->url = mb_trim(config('disposable-guard.integrations.numcheckr.url'));
        $this->apiKey = config('disposable-guard.integrations.numcheckr.api_key');
    }

    public function post(array $data): array
    {
       return $this->send('post', $this->url ,$data);
    }

    private function send(string $method, string $url, ?array $data): array
    {
        try {
            $response = $this->call($method, $url, $data);
        } catch (RequestException $e) {
            throw new RuntimeException();
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

    public function isDisposable(string $number): bool
    {
         return $this->send('post', $this->url , ['number' => $number])['is_disposable'];
    }
}
