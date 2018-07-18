<?php
namespace onix;

use Yii;
use yii\base\InvalidConfigException;

/**
 * Global configuration helper class.
 *
 */
class Env
{
    /**
     * Convert a language string in yii\i18n format to a ISO-639 format (2 or 3 letter code).
     *
     * @param string $language the input language string
     *
     * @return string
     */
    public static function getLang($language)
    {
        $pos = strpos($language, '-');
        return $pos > 0 ? substr($language, 0, $pos) : $language;
    }

    /**
     * Get the current directory of the extended class object
     *
     * @param object $object the called object instance
     *
     * @return string
     */
    public static function getCurrentDir($object)
    {
        if (empty($object)) {
            return '';
        }

        $child = new \ReflectionClass($object);
        return dirname($child->getFileName());
    }

    /**
     * Check if a file exists
     *
     * @param string $file the file with path in URL format
     *
     * @return bool
     */
    public static function fileExists($file)
    {
        $file = str_replace('/', DIRECTORY_SEPARATOR, $file);
        return file_exists($file);
    }
}