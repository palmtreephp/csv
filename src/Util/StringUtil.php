<?php

namespace Palmtree\Csv\Util;

class StringUtil
{
    const BOM_UTF8 = "\xEF\xBB\xBF";
    const BOM_UTF16_BE = "\xFE\xFF";
    const BOM_UTF16_LE = "\xFF\xFE";
    const BOM_UTF32_BE = "\x00\x00\xFE\xFF";
    const BOM_UTF32_LE = "\xFF\xFE\x00\x00";

    /**
     * Returns whether the given string starts with a UTF-8 Byte Order Mark.
     *
     * @param string $input
     *
     * @return bool
     */
    public static function hasBom($input, $bom)
    {
        return substr($input, 0, strlen($bom)) === $bom;
    }

    /**
     * Strips the UTF-8 Byte Order Mark from the beginning of a string
     * if it is present.
     *
     * @param string $input Data to be stripped of its BOM.
     *
     * @return string The stripped input string.
     */
    public static function stripBom($input, $bom)
    {
        if (self::hasBom($input, $bom)) {
            return substr($input, strlen($bom));
        }

        return $input;
    }
}
