<?php

declare(strict_types=1);

namespace Ladina\CFONB\Support;

/**
 * Small string helpers used to normalise data before it is written into a
 * fixed-width CFONB / AFB file.
 *
 * CFONB files are plain ASCII: accented or special characters must be folded
 * down to their closest ASCII equivalent (or removed) so the receiving bank
 * can parse them.
 */
final class Str
{
    /**
     * Transliteration map: characters that can be folded to an ASCII
     * equivalent instead of being dropped.
     *
     * @var array<string, string>
     */
    private const TRANSLITERATION = [
        // German
        'Ä' => 'Ae', 'Ö' => 'Oe', 'Ü' => 'Ue', 'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss',
        // others
        'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Å' => 'A', 'Ă' => 'A', 'Æ' => 'A',
        'Þ' => 'B', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
        'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
        'Ñ' => 'N', 'Ń' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ø' => 'O',
        'Š' => 'S', 'Ș' => 'S', 'Ț' => 'T',
        'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ý' => 'Y',
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'å' => 'a', 'ă' => 'a', 'æ' => 'a',
        'þ' => 'b', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ƒ' => 'f',
        'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
        'ñ' => 'n', 'ń' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ø' => 'o', 'ð' => 'o',
        'ș' => 's', 'š' => 's', 'ț' => 't',
        'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ÿ' => 'y',
        'Ð' => 'Dj', 'Ž' => 'Z', 'ž' => 'z',
    ];

    /**
     * Fold a value down to upper-case ASCII suitable for a CFONB file.
     *
     * Accented characters are transliterated, everything is upper-cased and any
     * character outside the CFONB allowed set is dropped.
     */
    public static function sanitize(string|int|float|null $value): string
    {
        $string = strtoupper(strtr((string) $value, self::TRANSLITERATION));

        return (string) preg_replace('#[^A-Z0-9".)(/ -]#', '', $string);
    }

    /**
     * Turn a monetary amount into the integer string expected by CFONB
     * (no decimal separator, decimals appended as digits).
     *
     * Example: remapAmount(10000, 2) === "1000000"
     */
    public static function remapAmount(int|float|string $amount, int $decimal = 2): string
    {
        return number_format((float) $amount, $decimal, '', '');
    }
}
