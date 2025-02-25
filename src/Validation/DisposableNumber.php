<?php

namespace Incentads\Disposable\Validation;

use Incentads\Disposable\Core\Phone\PhoneDecisionNode;
use Exception;
use Illuminate\Validation\Validator;

final class DisposableNumber
{
    /**
     * Default error message.
     *
     * @var string
     */
    public static string $errorMessage = 'Disposable phone numbers are not allowed.';

    /**
     * Validates whether a phone number does not originate from a disposable number service.
     *
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters
     * @param Validator $validator
     * @return bool
     * @throws Exception
     */
    public function validate(string $attribute, mixed $value, array $parameters, Validator $validator): bool
    {
        return app(PhoneDecisionNode::class)->handle($value);
    }
}
