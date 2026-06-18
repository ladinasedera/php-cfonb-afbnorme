<?php

declare(strict_types=1);

namespace Ladina\CFONB\Factory;

use Ladina\CFONB\AFB160;
use Ladina\CFONB\AFB320;
use Ladina\CFONB\CFONB;
use Ladina\CFONB\Exception\InvalidArgumentException;

/**
 * Convenient entry point to build a CFONB / AFB generator.
 *
 * Typical usage:
 *
 *     use Ladina\CFONB\Factory\CFONBFactory;
 *
 *     $cfonb = CFONBFactory::afb320([
 *         'emetteur'      => [...],
 *         'destinataires' => [...],
 *     ]);
 *
 *     echo $cfonb->build(true);
 */
final class CFONBFactory
{
    /**
     * Build a generator for the given AFB norm ("160" or "320").
     *
     * @throws InvalidArgumentException When the norm is unknown or data is missing.
     */
    public static function generateAFB(string $afbNorme = '320', array $data = []): CFONB
    {
        return match ($afbNorme) {
            '320' => self::buildAFB320($data),
            '160' => self::buildAFB160($data),
            default => throw new InvalidArgumentException(
                sprintf('Unknown AFB norm "%s", expected "160" or "320".', $afbNorme)
            ),
        };
    }

    /**
     * Build an AFB320 (international transfer) generator.
     *
     * @throws InvalidArgumentException
     */
    public static function afb320(array $data): AFB320
    {
        return self::buildAFB320($data);
    }

    /**
     * Build an AFB160 (domestic transfer) generator.
     *
     * @throws InvalidArgumentException
     */
    public static function afb160(array $data): AFB160
    {
        return self::buildAFB160($data);
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function buildAFB320(array $data): AFB320
    {
        return new AFB320('320', $data);
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function buildAFB160(array $data): AFB160
    {
        return new AFB160('160', $data);
    }
}
