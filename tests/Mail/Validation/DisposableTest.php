<?php

namespace Incentads\Disposable\Tests\Mail\Validation;

use Incentads\Disposable\Tests\Mail\EmailTestCase;
use Incentads\Disposable\Validation\DisposableEmail;
use PHPUnit\Framework\Attributes\Test;

class DisposableTest extends EmailTestCase
{
    #[Test]
    public function it_should_pass_for_legit_emails(): void
    {
        $validator = new DisposableEmail();
        $email = 'example@gmail.com';

        $this->assertTrue($validator->validate(null, $email, null, null));
    }

    #[Test]
    public function it_should_fail_for_disposable_emails(): void
    {
        $validator = new DisposableEmail();
        $email = 'example@yopmail.com';

        $this->assertFalse($validator->validate(null, $email, null, null));
    }

    #[Test]
    public function it_is_usable_through_the_validator(): void
    {
        $passingValidation = $this->app['validator']->make(['email' => 'example@gmail.com'], ['email' => 'disposable_email']);
        $failingValidation = $this->app['validator']->make(['email' => 'example@yopmail.com'], ['email' => 'disposable_email']);

        $this->assertTrue($passingValidation->passes());
        $this->assertTrue($failingValidation->fails());
    }
}
