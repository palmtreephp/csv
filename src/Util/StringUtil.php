<?php

declare(strict_types=1);

namespace Palmtree\Csv\Util;

class StringUtil
{
    public const string BOM_UTF8 = "\xEF\xBB\xBF";
    public const string BOM_UTF16_BE = "\xFE\xFF";
    public const string BOM_UTF16_LE = "\xFF\xFE";
    public const string BOM_UTF32_BE = "\x00\x00\xFE\xFF";
    public const string BOM_UTF32_LE = "\xFF\xFE\x00\x00";

    /**
     * Characters that make spreadsheet applications treat a cell as a formula.
     *
     * @see https://owasp.org/www-community/attacks/CSV_Injection
     */
    public const array FORMULA_CHARACTERS = ['=', '+', '-', '@', "\t", "\r"];

    /**
     * Returns whether the given string starts with the given Byte Order Mark.
     */
    public static function hasBom(string $input, string $bom): bool
    {
        return str_starts_with($input, $bom);
    }

    /**
     * Strips a Byte Order Mark from the beginning of a string if it is present.
     */
    public static function stripBom(string $input, string $bom): string
    {
        if (self::hasBom($input, $bom)) {
            return substr($input, \strlen($bom));
        }

        return $input;
    }

    /**
     * Prefixes string values that start with a formula character with a single quote, so
     * spreadsheet applications display them as text instead of evaluating them.
     *
     * Integers and floats are left alone, so negative numbers are still written as numbers.
     *
     * @see https://owasp.org/www-community/attacks/CSV_Injection
     *
     * @template TKey of array-key
     *
     * @param array<TKey, mixed> $data
     *
     * @return array<TKey, mixed>
     */
    public static function escapeFormulas(array $data): array
    {
        foreach ($data as $key => $value) {
            if (!\is_string($value) && !$value instanceof \Stringable) {
                continue;
            }

            $value = (string)$value;

            if ($value !== '' && \in_array($value[0], self::FORMULA_CHARACTERS, true)) {
                $data[$key] = "'" . $value;
            }
        }

        return $data;
    }

    /**
     * Converts each value to a string and escapes the enclosure character.
     * RFC-4180 states the enclosure character (usually double quotes) should be
     * escaped by itself, so " becomes "".
     *
     * @see https://tools.ietf.org/html/rfc4180#section-2
     *
     * @template TKey of array-key
     *
     * @param array<TKey, mixed> $data
     *
     * @return array<TKey, string>
     */
    public static function escapeEnclosure(array $data, string $enclosure): array
    {
        foreach ($data as $key => $value) {
            if ($value === null || \is_scalar($value) || $value instanceof \Stringable) {
                $data[$key] = str_replace($enclosure, str_repeat($enclosure, 2), (string)$value);
            } else {
                throw new \InvalidArgumentException(\sprintf('Cannot convert value of type %s to a string.', get_debug_type($value)));
            }
        }

        return $data;
    }
}
