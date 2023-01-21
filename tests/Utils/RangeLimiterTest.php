<?php

namespace App\Tests\Utils;

use PHPUnit\Framework\TestCase;
use App\Utils\RangeLimiter;

class RangeLimiterTest
{
    /** @test */
    public function clamp_should_return_the_value_as_is_if_it_is_already_in_the_range(): int
    {
        $this->assertSame(12, RangeLimiter::clamp(12, 8, 60));
    }

    /** @test */
    public function clamp_should_return_the_min_value_if_value_is_less_than_min(): int
    {
        $this->assertSame(8, RangeLimiter::clamp(5, 8, 60));
    }

    /** @test */
    public function clamp_should_return_the_max_value_if_value_is_greater_than_max(): int
    {
        $this->assertSame(60, RangeLimiter::clamp(100, 8, 60));
    }
}
