<?php

namespace Incentads\Disposable\Validation;

use Incentads\Disposable\Facades\DisposableDomains;
use Illuminate\Validation\Validator;

final class DisposableEmail
{
    /**
     * Default error message.
     *
     * @var string
     */
    public static string $errorMessage = 'Disposable email addresses are not allowed.';

    public function validate(?string $attribute, mixed $value, ?array $parameters, ?Validator $validator = null): bool
    {
        return DisposableDomains::isLegit($value);
    }
}
