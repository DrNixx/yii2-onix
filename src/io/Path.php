<?php
namespace onix\io;

use onix\exceptions\ArgumentException;
use onix\exceptions\ArgumentNullException;

final class Path
{
    public static function combine($path1, $path2, $separator = DIRECTORY_SEPARATOR)
    {
        if ($path1 === null) {
            throw new ArgumentNullException("path1");
        }

        if ($path2 === null) {
            throw new ArgumentNullException("path2");
        }

        if (strlen($path1) === 0) {
            return $path2;
        }

        if (strlen($path2) === 0) {
            return $path1;
        }

        foreach (self::getInvalidPathChars() as $ch) {
            if (strpos($path1, $ch) !== false) {
                throw new ArgumentException("Illegal characters in path.", "path1");
            }
        }

        foreach (self::getInvalidPathChars() as $ch) {
            if (strpos($path2, $ch) !== false) {
                throw new ArgumentException("Illegal characters in path.", "path2");
            }
        }

        return rtrim($path1, $separator) . $separator . ltrim($path2, $separator);
    }

    public static function isRooted($path)
    {
        if (empty($path)) {
            return false;
        }

        if ($path[0] == DIRECTORY_SEPARATOR) {
            return true;
        }

        if (DIRECTORY_SEPARATOR == "\\") {
            return (strlen($path) > 1) && ($path[1] == ":");
        }

        return false;
    }

    private static function getInvalidPathChars()
    {
        // return a new array as we do not want anyone to be able to change the values
        if (DIRECTORY_SEPARATOR == "\\") {
            return array("\x22", "\x3C", "\x3E", "\x7C", "\x00", "\x01", "\x02", "\x03", "\x04", "\x05", "\x06", "\x07",
                "\x08", "\x09", "\x0A", "\x0B", "\x0C", "\x0D", "\x0E", "\x0F", "\x10", "\x11", "\x12", "\x13", "\x14",
                "\x15", "\x16", "\x17", "\x18", "\x19", "\x1A", "\x1B", "\x1C", "\x1D", "\x1E", "\x1F" );
        } else {
            return array ( "\x00" );
        }
    }

    /**
     * Subtraction of a part $part from a full path $path
     *
     * @param string $path Полный путь
     * @param string $part Часть пути
     * @param string $separator
     *
     * @return string
     */
    public static function pathSub($path, $part, $separator = DIRECTORY_SEPARATOR)
    {
        if (empty($path)) {
            throw new ArgumentNullException("path");
        }

        if (empty($part)) {
            throw new ArgumentNullException("part");
        }

        $pattern = '@[/\\\\]+@';
        $path = preg_replace($pattern, DIRECTORY_SEPARATOR, $path);
        $part = preg_replace($pattern, DIRECTORY_SEPARATOR, $part.DIRECTORY_SEPARATOR);
        $part = preg_quote($part, '@');
        $result = preg_replace('@^'.$part.'@', DIRECTORY_SEPARATOR, $path);
        if ($separator != DIRECTORY_SEPARATOR) {
            $result = str_replace(DIRECTORY_SEPARATOR, $separator, $result);
        }

        return $result;
    }
}
