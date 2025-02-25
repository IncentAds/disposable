<?php

namespace Incentads\Disposable\Utils;

class ArrayHelper
{
    public static function combineKeysValues(array $items): array
    {
        return array_combine(array_values($items), $items);
    }

}
