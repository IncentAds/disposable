<?php

namespace CristianPeter\LaravelDisposableContactGuard\Fetcher\phone;

use CristianPeter\LaravelDisposableContactGuard\Contracts\Fetcher;
use InvalidArgumentException;
use UnexpectedValueException;

class DefaultPhoneFetcher implements Fetcher
{

    public function handle($url): array
    {
        if (! $url) {
            throw new InvalidArgumentException('Source URL is null');
        }

        $content = file_get_contents($url);

        if ($content === false) {
            throw new UnexpectedValueException('Failed to interpret the source URL ('.$url.')');
        }


        if (! $this->isValidJson($content)) {
            throw new UnexpectedValueException('Provided data could not be parsed as JSON');
        }
        return array_flip(array_keys(json_decode($content,  flags: JSON_OBJECT_AS_ARRAY)));
    }

    protected function isValidJson($data): bool
    {
        $data = json_decode($data, true);

        return json_last_error() === JSON_ERROR_NONE && ! empty($data);
    }
}
