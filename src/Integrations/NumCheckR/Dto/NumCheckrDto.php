<?php

namespace CristianPeter\LaravelDisposableContactGuard\Integrations\NumCheckR\Dto;

class NumCheckrDto
{
    public function __construct(
        private bool $isValid,
        private bool $isDisposable,
        private bool $type,
        private string $countryCode,
        private string $countryName,
        private string $carrier,
        private string $e16Format,
        private string $nationalFormat,
        private string $internationalFormat,
    ){

    }


}