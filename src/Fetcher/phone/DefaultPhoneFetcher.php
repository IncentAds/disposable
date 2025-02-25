<?php

namespace Incentads\Disposable\Fetcher\phone;

use Incentads\Disposable\Fetcher\Fetcher;
use Incentads\Disposable\Utils\ArrayHelper;
use InvalidArgumentException;
use UnexpectedValueException;

class DefaultPhoneFetcher implements Fetcher
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
        $data = json_decode($content, flags: JSON_OBJECT_AS_ARRAY);
        $result = $this->parseE16Format($data);
        return ArrayHelper::combineKeysValues($result);
    }

    /**
     * @param mixed $data
     * @return array
     */
    public function parseE16Format(mixed $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            $result[] = '+' . $key;
        }
        return $result;
    }

    protected function isValidJson($data): bool
    {
        $data = json_decode($data, true);

        return JSON_ERROR_NONE === json_last_error() && ! empty($data);
    }
}
