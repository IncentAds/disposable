<?php

namespace Incentads\Disposable\Fetcher\email;

use Incentads\Disposable\Fetcher\Fetcher;
use Incentads\Disposable\Utils\ArrayHelper;
use InvalidArgumentException;
use UnexpectedValueException;

class DefaultEmailFetcher implements Fetcher
{
    public function handle($url): array
    {
        if ( ! $url) {
            throw new InvalidArgumentException('Source URL is null');
        }

        $content = file_get_contents($url);

        if (false === $content) {
            throw new UnexpectedValueException('Failed to interpret the source URL (' . $url . ')');
        }

        if ( ! $this->isValidJson($content)) {
            throw new UnexpectedValueException('Provided data could not be parsed as JSON');
        }

        return ArrayHelper::combineKeysValues(json_decode($content));
    }

    protected function isValidJson($data): bool
    {
        $data = json_decode($data, true);

        return JSON_ERROR_NONE === json_last_error() && ! empty($data);
    }
}
