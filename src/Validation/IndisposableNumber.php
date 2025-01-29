<?php

namespace CristianPeter\LaravelDisposableContactGuard\Validation;

use CristianPeter\LaravelDisposableContactGuard\Core\Phone\PhoneDecisionNode;
use Exception;
use Illuminate\Validation\Validator;

class IndisposableNumber
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
    public function validate($attribute, $value, $parameters, $validator): bool
    {
        return app(PhoneDecisionNode::class)->handle($value);
    }
}
