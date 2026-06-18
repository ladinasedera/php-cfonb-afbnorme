<?php

declare(strict_types=1);

namespace Ladina\CFONB\Tests\Support;

use Ladina\CFONB\Support\Str;
use PHPUnit\Framework\TestCase;

final class StrTest extends TestCase
{
    /**
     * @dataProvider sanitizeProvider
     */
    public function testSanitize(string|int|float|null $input, string $expected): void
    {
        self::assertSame($expected, Str::sanitize($input));
    }

    public static function sanitizeProvider(): array
    {
        return [
            'uppercases'              => ['dupont', 'DUPONT'],
            'transliterates accents'  => ['Crédit Agéçôle', 'CREDIT AGECOLE'],
            'german umlauts'          => ['Müller', 'MUELLER'],
            'strips forbidden chars'  => ['A_B#C', 'ABC'],
            'keeps allowed punctuation' => ['REF.1 (X)/Y-Z', 'REF.1 (X)/Y-Z'],
            'null becomes empty'      => [null, ''],
            'integer is stringified'  => [42, '42'],
        ];
    }

    /**
     * @dataProvider remapAmountProvider
     */
    public function testRemapAmount(int|float|string $amount, int $decimals, string $expected): void
    {
        self::assertSame($expected, Str::remapAmount($amount, $decimals));
    }

    public static function remapAmountProvider(): array
    {
        return [
            'integer with 2 decimals'  => [10000, 2, '1000000'],
            'float with 2 decimals'    => [123.45, 2, '12345'],
            'no decimals'              => [10000, 0, '10000'],
            'float half'               => [1.5, 2, '150'],
            'numeric string'           => ['250.50', 2, '25050'],
        ];
    }
}
