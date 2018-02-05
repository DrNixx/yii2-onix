<?php
namespace onix\i18n;

use IntlDateFormatter;
use Yii;
use yii\i18n\Formatter as BaseFormatter;

class Formatter extends BaseFormatter
{

    /**
     * @param \DateTime|int|string $value
     * @param null $format
     *
     * @return string
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function asDate($value, $format = null)
    {
        if ($format == parent::FORMAT_WIDTH_SHORT) {
            $formatter = new IntlDateFormatter(Yii::$app->language, IntlDateFormatter::SHORT, IntlDateFormatter::NONE);
            $format = preg_replace(':(^|[^y])y{2,2}([^y]|$):', '$1yyyy$2', $formatter->getPattern());
        }

        return parent::asDate($value, $format);
    }

    /**
     * @param \DateTime|int|string $value
     * @param null $format
     *
     * @return string
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function asDatetime($value, $format = null)
    {
        if ($format == parent::FORMAT_WIDTH_SHORT) {
            $formatter = new IntlDateFormatter(Yii::$app->language, IntlDateFormatter::SHORT, IntlDateFormatter::NONE);
            $format = preg_replace(':(^|[^y])y{2,2}([^y]|$):', '$1yyyy$2', $formatter->getPattern()).':ss';
        }

        return parent::asDatetime($value, $format);
    }
}
