<?php

namespace CristianPeter\LaravelDisposableContactGuard\Adaptors\NumCheckR;

use CristianPeter\LaravelDisposableContactGuard\Adaptors\NumCheckR\client\NumCheckrClient;
use CristianPeter\LaravelDisposableContactGuard\Adaptors\NumCheckR\Dto\NumCheckrDto;
use CristianPeter\LaravelDisposableContactGuard\Adaptors\NumCheckR\Factory\NumCheckrDtoFactory;

class NumCheckManager implements NumCheckrApiInterface
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
