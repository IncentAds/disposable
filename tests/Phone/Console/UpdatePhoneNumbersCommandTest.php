<?php

namespace CristianPeter\LaravelDisposableContactGuard\Tests\Phone\Console;


use CristianPeter\LaravelDisposableContactGuard\Tests\Phone\PhoneTestCase;
use PHPUnit\Framework\Attributes\Test;

class UpdatePhoneNumbersCommandTest extends PhoneTestCase
{
    #[Test]
    public function it_creates_the_file()
    {
        $this->assertFileDoesNotExist($this->storagePath);

        $this->artisan('disposable-numbers:update')
            ->assertExitCode(0);

        $this->assertFileExists($this->storagePath);

        $numbers = $this->disposable()->getNumbers();

        $this->assertIsArray($numbers);
    }

    #[Test]
    public function it_overwrites_the_file()
    {
        file_put_contents($this->storagePath, json_encode(['622134090']));

        $this->artisan('disposable:update')
            ->assertExitCode(0);

        $this->assertFileExists($this->storagePath);

        $domains = $this->disposable()->getNumbers();

        $this->assertIsArray($domains);
        $this->assertNotContains('622134090', $domains);
    }
}
