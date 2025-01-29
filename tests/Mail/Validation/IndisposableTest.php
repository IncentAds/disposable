<?php

namespace CristianPeter\LaravelDisposableContactGuard\Tests\Mail\Validation;

use CristianPeter\LaravelDisposableContactGuard\Tests\Mail\EmailTestCase;
use CristianPeter\LaravelDisposableContactGuard\Validation\IndisposableEmail;
use PHPUnit\Framework\Attributes\Test;

class IndisposableTest extends EmailTestCase
{
    #[Test]
    public function it_should_pass_for_indisposable_emails()
    {
        $validator = new IndisposableEmail;
        $email = 'example@gmail.com';

        $this->assertTrue($validator->validate(null, $email, null, null));
    }

    #[Test]
    public function it_should_fail_for_disposable_emails()
    {
        $validator = new IndisposableEmail;
        $email = 'example@yopmail.com';

        $this->assertFalse($validator->validate(null, $email, null, null));
    }

    #[Test]
    public function it_is_usable_through_the_validator()
    {
        $passingValidation = $this->app['validator']->make(['email' => 'example@gmail.com'], ['email' => 'indisposable_email']);
        $failingValidation = $this->app['validator']->make(['email' => 'example@yopmail.com'], ['email' => 'indisposable_email']);

        $this->assertTrue($passingValidation->passes());
        $this->assertTrue($failingValidation->fails());
    }
}
