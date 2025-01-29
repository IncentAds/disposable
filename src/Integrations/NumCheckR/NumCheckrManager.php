<?php

namespace CristianPeter\LaravelDisposableContactGuard\Integrations\NumCheckR;

use CristianPeter\LaravelDisposableContactGuard\Integrations\NumCheckR\Client\NumCheckrClient;
use CristianPeter\LaravelDisposableContactGuard\Integrations\NumCheckR\Dto\NumCheckrDto;
use CristianPeter\LaravelDisposableContactGuard\Integrations\NumCheckR\Factory\NumCheckrDtoFactory;

class NumCheckrManager implements NumCheckrApiInterface
{
    public function __construct(private readonly NumCheckrClient $client, private readonly NumCheckrDtoFactory $factory)
    {
    }
    public function info(string $number): NumCheckrDto
    {
        $data = $this->client->post(['phone' => $number]);
        return $this->factory->createFromResponse($data);
    }

    public function isDisposable(string $number):bool
    {
        return $this->client->isDisposable($number);
    }


}
