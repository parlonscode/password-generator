<?php

namespace App\Tests\Utils;

use PHPUnit\Framework\TestCase;
use App\Utils\PasswordGenerator;

class PasswordGeneratorTest extends TestCase
{
    /** @test */
    public function generate_should_respect_password_constraints(): void
    {
        $password = PasswordGenerator::generate(length: 10);
        $this->assertSame(10, mb_strlen($password));
        $this->assertMatchesRegularExpression('/^[a-z]{10}$/', $password);
        $this->assertDoesNotMatchRegularExpression('/[A-Z]/', $password);
        $this->assertDoesNotMatchRegularExpression('/[0-9]/', $password);
        $this->assertDoesNotMatchRegularExpression('/[\W_]/', $password);

        $password = PasswordGenerator::generate(length: 12, uppercaseLetters: true);
        $this->assertSame(12, mb_strlen($password));
        $this->assertMatchesRegularExpression('/[a-z]/', $password);
        $this->assertMatchesRegularExpression('/[A-Z]/', $password);
        $this->assertDoesNotMatchRegularExpression('/[0-9]/', $password);
        $this->assertDoesNotMatchRegularExpression('/[\W_]/', $password);

        $password = PasswordGenerator::generate(length: 16, uppercaseLetters: true, digits: true);
        $this->assertSame(16, mb_strlen($password));
        $this->assertMatchesRegularExpression('/[a-z]/', $password);
        $this->assertMatchesRegularExpression('/[A-Z]/', $password);
        $this->assertMatchesRegularExpression('/[0-9]/', $password);
        $this->assertDoesNotMatchRegularExpression('/[\W_]/', $password);

        $password = PasswordGenerator::generate(length: 9, uppercaseLetters: true, digits: true, specialCharacters: true);
        $this->assertSame(9, mb_strlen($password));
        $this->assertMatchesRegularExpression('/[a-z]/', $password);
        $this->assertMatchesRegularExpression('/[A-Z]/', $password);
        $this->assertMatchesRegularExpression('/[0-9]/', $password);
        $this->assertMatchesRegularExpression('/[\W_]/', $password);
    }
}
