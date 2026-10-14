<?php

namespace App\Support;

use InvalidArgumentException;

final class Money
{
    public static function toCents(string $amount): int
    {
        if (! preg_match('/^\d+(\.\d+)?$/', $amount)) {
            throw new InvalidArgumentException('Invalid monetary amount.');
        }

        // Round half-up to two decimal places.
        $rounded = bcadd($amount, '0.005', 2);

        [$euros, $cents] = explode('.', $rounded);

        // Restrict conversion to the supported integer range.
        $maximumEuros = (string) intdiv(PHP_INT_MAX, 100);

        if (
            strlen($euros) > strlen($maximumEuros)
            || (
                strlen($euros) === strlen($maximumEuros)
                && strcmp($euros, $maximumEuros) > 0
            )
        ) {
            throw new InvalidArgumentException('Monetary amount is too large.');
        }

        $result = ((int) $euros * 100) + (int) $cents;

        if ($result < 0) {
            throw new InvalidArgumentException('Monetary amount is too large.');
        }

        return $result;
    }

    public static function multiplyToCents(
        string $quantity,
        string $unitPrice
    ): int {
        $amount = bcmul($quantity, $unitPrice, 8);

        return self::toCents($amount);
    }

    public static function percentageOfCents(
        int $amountCents,
        string $percentage
    ): int {
        $amount = bcmul(
            (string) $amountCents,
            $percentage,
            6
        );

        $result = bcdiv($amount, '100', 6);

        // The result is already in cents.
        return (int) bcadd($result, '0.5', 0);
    }

    public static function format(int $cents): string
    {
        $euros = intdiv($cents, 100);
        $remainder = $cents % 100;

        return $euros . '.' . str_pad(
                (string) $remainder,
                2,
                '0',
                STR_PAD_LEFT
            );
    }
}
