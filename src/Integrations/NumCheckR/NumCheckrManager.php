<?php

namespace CristianPeter\LaravelDisposableContactGuard\Integrations\NumCheckR;

use CristianPeter\LaravelDisposableContactGuard\Integrations\NumCheckR\Client\NumCheckrClient;
use CristianPeter\LaravelDisposableContactGuard\Integrations\NumCheckR\Dto\NumCheckrDto;
use CristianPeter\LaravelDisposableContactGuard\Integrations\NumCheckR\Exceptions\ApiErrorException;
use CristianPeter\LaravelDisposableContactGuard\Integrations\NumCheckR\Factory\NumCheckrDtoFactory;
use Illuminate\Http\Client\ConnectionException;

final readonly class NumCheckrManager implements NumCheckrApiInterface
{
    public function __construct(private NumCheckrClient $client, private NumCheckrDtoFactory $factory)
    {
    }

    /**
     * @throws ApiErrorException
     * @throws ConnectionException
     */
    public function info(string $number): NumCheckrDto
    {
        $data = $this->client->post(['phone' => $number]);
        return $this->factory->createFromResponse($data);
    }

    /**
     * @throws ApiErrorException
     * @throws ConnectionException
     */
    public function isDisposable(string $number):bool
    {
        return $this->client->isDisposable($number);
    }


}
