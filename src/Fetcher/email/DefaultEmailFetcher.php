<?php

namespace CristianPeter\LaravelDisposableContactGuard\Fetcher\email;

use CristianPeter\LaravelDisposableContactGuard\Fetcher\Fetcher;
use CristianPeter\LaravelDisposableContactGuard\Utils\ArrayHelper;
use InvalidArgumentException;
use UnexpectedValueException;

class DefaultEmailFetcher implements Fetcher
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

        return ArrayHelper::combineKeysValues(json_decode($content));
    }

    protected function isValidJson($data): bool
    {
        $data = json_decode($data, true);

        return json_last_error() === JSON_ERROR_NONE && ! empty($data);
    }
}
