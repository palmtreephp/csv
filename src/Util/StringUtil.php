<?php

declare(strict_types=1);

namespace Palmtree\Csv\Util;

class StringUtil
{
    public const BOM_UTF8 = "\xEF\xBB\xBF";
    public const BOM_UTF16_BE = "\xFE\xFF";
    public const BOM_UTF16_LE = "\xFF\xFE";
    public const BOM_UTF32_BE = "\x00\x00\xFE\xFF";
    public const BOM_UTF32_LE = "\xFF\xFE\x00\x00";

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
     * Escapes the enclosure character recursively.
     * RFC-4180 states the enclosure character (usually double quotes) should be
     * escaped by itself, so " becomes "".
     *
     * @see https://tools.ietf.org/html/rfc4180#section-2
     */
    public static function escapeEnclosure(array $data, string $enclosure): array
    {
        foreach ($data as $key => $value) {
            if (\is_array($value)) {
                $data[$key] = self::escapeEnclosure($value, $enclosure);
            } else {
                $data[$key] = str_replace($enclosure, str_repeat($enclosure, 2), $value);
            }
        }

        return $data;
    }
}
