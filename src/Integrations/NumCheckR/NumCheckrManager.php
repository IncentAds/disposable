<?php

namespace Incentads\Disposable\Integrations\NumCheckR;

use Illuminate\Http\Client\ConnectionException;
use Incentads\Disposable\Integrations\NumCheckR\Client\NumCheckrClient;
use Incentads\Disposable\Integrations\NumCheckR\Dto\NumCheckrDto;
use Incentads\Disposable\Integrations\NumCheckR\Exceptions\ApiErrorException;
use Incentads\Disposable\Integrations\NumCheckR\Factory\NumCheckrDtoFactory;

final readonly class NumCheckrManager implements NumCheckrApiInterface
{
    public function __construct(private NumCheckrClient $client, private NumCheckrDtoFactory $factory) {}

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
    public function isNotDisposable(string $number): bool
    {
        return ! $this->client->isDisposable($number);
    }

    /**
     * @throws ApiErrorException
     * @throws ConnectionException
     */
    public function isDisposable(string $number): bool
    {
        return $this->client->isDisposable($number);
    }

}
