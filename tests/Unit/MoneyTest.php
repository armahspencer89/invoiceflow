<?php

namespace Tests\Unit;

use App\Support\Money;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function test_converts_euros_to_cents(): void
    {
        $this->assertSame(1999, Money::toCents('19.99'));
        $this->assertSame(10000, Money::toCents('100.00'));
        $this->assertSame(0, Money::toCents('0'));
    }

    public function test_rounds_half_up_to_nearest_cent(): void
    {
        $this->assertSame(1001, Money::toCents('10.005'));
        $this->assertSame(0, Money::toCents('0.004'));
        $this->assertSame(1, Money::toCents('0.005'));
    }

    public function test_multiplies_decimal_quantities_correctly(): void
    {
        $this->assertSame(
            2499,
            Money::multiplyToCents('1.25', '19.99')
        );

        $this->assertSame(
            5997,
            Money::multiplyToCents('3', '19.99')
        );
    }

    public function test_calculates_vat_with_correct_rounding(): void
    {
        $this->assertSame(
            2100,
            Money::percentageOfCents(10000, '21')
        );

        $this->assertSame(
            1,
            Money::percentageOfCents(5, '21')
        );

        $this->assertSame(
            0,
            Money::percentageOfCents(2, '21')
        );
    }

    public function test_accepts_maximum_supported_integer_amount(): void
    {
        $maximumAmount = substr((string) PHP_INT_MAX, 0, -2)
            . '.'
            . substr((string) PHP_INT_MAX, -2);

        $this->assertSame(
            PHP_INT_MAX,
            Money::toCents($maximumAmount)
        );
    }

    public function test_rejects_amounts_exceeding_integer_limit(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $tooLarge = bcadd((string) PHP_INT_MAX, '1', 0);

        $amount = substr($tooLarge, 0, -2)
            . '.'
            . substr($tooLarge, -2);

        Money::toCents($amount);
    }

    public function test_rejects_invalid_monetary_amounts(): void
    {
        foreach (['-10.00', 'abc', '10,50', ''] as $invalid) {
            try {
                Money::toCents($invalid);
                $this->fail("Expected exception for: {$invalid}");
            } catch (InvalidArgumentException $exception) {
                $this->assertSame(
                    'Invalid monetary amount.',
                    $exception->getMessage()
                );
            }
        }
    }
}
