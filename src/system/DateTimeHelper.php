<?php
namespace onix\system;

use MongoId;
use Yii;

class DateTimeHelper
{
    /**
     * Minimal date
     *
     * @return \DateTime
     */
    final public static function minDate()
    {
        return date_create('1900-01-01', new \DateTimeZone('UTC'));
    }

    /**
     * Current date
     *
     * @return \DateTime
     */
    final public static function now()
    {
        return date_create(null, new \DateTimeZone('UTC'));
    }

    /**
     * Current date in SQL format
     *
     * @return string
     */
    final public static function nowSql()
    {
        return date_create(null, new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');
    }

    /**
     * Date in SQL format
     * If date is empty now date returned
     *
     * @param string $str
     *
     * @return string
     */
    final public static function dateOrNowSql($str)
    {
        return !empty($str) ? $str : date_create(null, new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');
    }


    /**
     * Date in SQL format
     *
     * @param \DateTime|string $date
     *
     * @return string|null
     */
    final public static function asSql($date)
    {
        if (!empty($date)) {
            if ($date instanceof \DateTime) {
                return $date->format('Y-m-d H:i:s');
            } else {
                return self::asDateTime($date)->format('Y-m-d H:i:s');
            }
        } else {
            return null;
        }
    }

    /**
     * Convert string date to [[DateTime]] object
     *
     * @param $str
     * @param string $tz
     *
     * @return \DateTime
     */
    final public static function asDateTime($str, $tz = 'UTC')
    {
        return date_create($str, new \DateTimeZone($tz));
    }

    /**
     * @param $str
     * @param string $tz
     *
     * @return int
     */
    final public static function asTimestamp($str, $tz = 'UTC')
    {
        return self::asDateTime($str, $tz)->getTimestamp();
    }

    /**
     * @param MongoId $mongoId
     * @param string $tz
     *
     * @return \DateTime
     */
    final public static function getDateTimeFromMongoId($mongoId, $tz = 'UTC')
    {
        $dateTime = new \DateTime('@'.$mongoId->getTimestamp());
        $dateTime->setTimezone(new \DateTimeZone($tz));
        return $dateTime;
    }

    /**
     * @param $str
     * @param string $tz
     *
     * @return int
     */
    final public static function asJavaScriptTimestamp($str, $tz = 'UTC')
    {
        $datePart = $str;
        $msPart = 0;
        if (strpos($str, '.') !== false) {
            $parts = explode('.', $str);
            $datePart = $parts[0];
            $msPart = str_pad($parts[1], 6, '0', STR_PAD_RIGHT);
        }

        return self::asDateTime($datePart, $tz)->getTimestamp() * 1000 + intval(intval($msPart) / 1000);
    }

    /**
     * @param \DateTime $date
     *
     * @return \DateTime
     */
    final public static function cloneDateTime($date)
    {
        $result = new \DateTime();
        $result->setTimezone($date->getTimezone());
        $result->setTimestamp($date->getTimestamp());
        return $result;
    }

    /**
     * @param $offset
     * @param int|null $isDst
     *
     * @return string
     */
    final public static function tzOffsetToName($offset, $isDst = null)
    {
        if ($isDst === null) {
            $isDst = date('I');
        }

        $offset *= 3600;
        $zone = timezone_name_from_abbr('', $offset, $isDst);

        if ($zone === false) {
            foreach (timezone_abbreviations_list() as $abbr) {
                foreach ($abbr as $city) {
                    if (((bool)$city['dst'] === (bool)$isDst) &&
                        (strlen($city['timezone_id']) > 0) &&
                        ($city['offset'] == $offset)
                    ) {
                        $zone = $city['timezone_id'];
                        break;
                    }
                }

                if ($zone !== false) {
                    break;
                }
            }
        }

        return $zone ?: date_default_timezone_get();
    }

    /**
     * @param \DateInterval $interval
     *
     * @return \DateInterval
     */
    final public static function recalculateInterval($interval)
    {
        $from = new \DateTime;
        $to = clone $from;
        $to = $to->add($interval);
        $diff = $from->diff($to);
        foreach ($diff as $k => $v) {
            $interval->$k = $v;
        }

        return $interval;
    }

    /**
     * @param \DateInterval $interval
     *
     * @return string
     */
    final public static function intervalToSpec($interval)
    {
        $interval = self::recalculateInterval($interval);

        $result = "P";
        if ($interval->y !== 0) {
            $result .= sprintf('%dY', $interval->y);
        }

        if ($interval->m !== 0) {
            $result .= sprintf('%dM', $interval->m);
        }

        if ($interval->d !== 0) {
            $result .= sprintf('%dD', $interval->d);
        }


        $timeMarkerSet = false;
        if ($interval->h !== 0) {
            if (!$timeMarkerSet) {
                $result .= 'T';
            }

            $result .= sprintf('%dH', $interval->h);
        }

        if ($interval->i !== 0) {
            if (!$timeMarkerSet) {
                $result .= 'T';
            }

            $result .= sprintf('%dM', $interval->i);
        }

        if ($interval->s !== 0) {
            if (!$timeMarkerSet) {
                $result .= 'T';
            }

            $result .= sprintf('%dS', $interval->s);
        }

        if ($result == 'P') {
            return "";
        }

        return $result;
    }

    /**
     * @param int $secs
     *
     * @return string
     *
     * @throws \Exception
     */
    final public static function secondsToSpec($secs)
    {
        if (!empty($secs)) {
            $interval = self::secondsToInterval($secs);
            return self::intervalToSpec($interval);
        }

        return "";
    }

    /**
     * @param string $spec
     *
     * @return int
     *
     * @throws \Exception
     */
    final public static function specToSeconds($spec)
    {
        if (!empty($spec)) {
            if (is_string($spec) && (($spec[0] == "P") || ($spec[0] == "p"))) {
                $spec = strtoupper($spec);
                $interval = new \DateInterval($spec);
                return self::intervalToSeconds($interval);
            } else {
                return intval($spec);
            }
        }

        return 0;
    }

    /**
     * @param \DateInterval $interval
     * @param int $parts
     *
     * @return string
     */
    final public static function formatInterval($interval, $parts = 0)
    {
        $interval = self::recalculateInterval($interval);

        $format = [];
        if ($interval->y !== 0) {
            $format[] = Yii::t(
                'datetime',
                '{y,plural,one{# year} few{# years} many{# years} other{# years}}',
                ['y' => $interval->y]
            );
        }

        if ($interval->m !== 0) {
            $format[] = Yii::t(
                'datetime',
                '{m,plural,one{# month} few{# months} many{# months} other{# months}}',
                ['m' => $interval->m]
            );
        }

        if ($interval->d !== 0) {
            $format[] = Yii::t(
                'datetime',
                '{d,plural,one{# day} few{# days} many{# days} other{# days}}',
                ['d' => $interval->d]
            );
        }

        if ($interval->h !== 0) {
            $format[] = Yii::t(
                'datetime',
                '{h,plural,one{# hour} few{# hours} many{# hours} other{# hours}}',
                ['h' => $interval->h]
            );
        }

        if ($interval->i !== 0) {
            $format[] = Yii::t(
                'datetime',
                '{i,plural,one{#minute} few{# minutes} many{# minutes} other{# minutes}}',
                ['i' => $interval->i]
            );
        }

        if ($interval->s !== 0) {
            $format[] = Yii::t(
                'datetime',
                '{s,plural,one{# second} few{# seconds} many{# seconds} other{# seconds}}',
                ['s' => $interval->s]
            );
        }

        if (count($format) === 0) {
            return "";
        }

        if (($parts > 0) && (count($format) > 1)) {
            $values = [];
            while ((count($values) < $parts) && (count($format) > 0)) {
                $values[] = array_shift($format);
            }
        } else {
            $values = $format;
        }

        return implode(" ", $values);
    }

    /**
     * @param int $sec
     * @param int $parts
     *
     * @return string
     *
     * @throws \Exception
     */
    final public static function formatSeconds($sec, $parts = 0)
    {
        return self::formatInterval(self::secondsToInterval($sec), $parts);
    }

    final public static function formatDateDiff($start, $end = null)
    {
        if (!($start instanceof \DateTime)) {
            $start = new \DateTime($start);
        }

        if ($end === null) {
            $end = new \DateTime();
        }

        if (!($end instanceof \DateTime)) {
            $end = new \DateTime($start);
        }

        $interval = $end->diff($start);

        return self::formatInterval($interval);
    }

    /**
     * @param int $sec
     *
     * @return \DateInterval
     *
     * @throws \Exception
     */
    final public static function secondsToInterval($sec)
    {
        return new \DateInterval(sprintf('PT%dS', intval($sec)));
    }

    /**
     * @param \DateInterval $interval
     *
     * @return int
     */
    final public static function intervalToSeconds($interval)
    {
        $start = new \DateTimeImmutable();
        $end = $start->add($interval);

        return $end->getTimestamp() - $start->getTimestamp();
    }
}
