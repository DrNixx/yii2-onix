<?php
namespace onix\system;

use MongoId;
use Yii;

class DateTimeHelper
{
    /**
     * @var \DateTimeZone
     */
    private static $utc = null;

    /**
     * @var \DateTimeImmutable
     */
    private static $now = null;

    /**
     * Get UTC timezone
     *
     * @return \DateTimeZone
     */
    private static function tzUtc()
    {
        if (self::$utc === null) {
            self::$utc = new \DateTimeZone('UTC');
        }

        return self::$utc;
    }

    /**
     * @param \DateTimeZone|string $tz
     *
     * @return \DateTimeZone
     *
     * @noinspection PhpDocMissingThrowsInspection
     */
    private static function getTimeZone($tz)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return ($tz instanceof \DateTimeZone) ? $tz : new \DateTimeZone($tz);
    }

    /**
     * Minimal date
     *
     * @return \DateTimeInterface
     */
    final public static function minDate()
    {
        try {
            return new \DateTimeImmutable('1900-01-01', self::tzUtc());
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Current date
     *
     * @return \DateTimeInterface
     */
    final public static function now()
    {
        if (self::$now === null) {
            try {
                self::$now = new \DateTimeImmutable('now', self::tzUtc());
            } catch (\Exception $e) {
                return null;
            }
        }

        return self::$now;
    }

    /**
     * Current date in SQL format
     *
     * @return string
     */
    final public static function nowSql()
    {
        try {
            $date = new \DateTimeImmutable('now', self::tzUtc());
            return $date->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
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
        return !empty($str) ? $str : self::nowSql();
    }

    /**
     * Date in SQL format (UTC)
     *
     * @param \DateTime|\DateTimeImmutable|string $date
     *
     * @return string|null
     */
    final public static function asUtcSql($date)
    {
        if (!empty($date)) {
            if ($date instanceof \DateTimeInterface) {
                return $date->setTimezone(self::tzUtc())->format('Y-m-d H:i:s');
            } else {
                return self::asDateTime($date)->format('Y-m-d H:i:s');
            }
        } else {
            return null;
        }
    }

    /**
     * Date in SQL format
     *
     * @param \DateTimeInterface|string $date
     * @param bool $withTz
     *
     * @return string|null
     */
    final public static function asSql($date, $withTz = true)
    {
        $format = $withTz ? 'Y-m-d H:i:sO' : 'Y-m-d H:i:s';
        if (!empty($date)) {
            if ($date instanceof \DateTimeInterface) {
                return $date->format($format);
            } else {
                return self::asDateTime($date)->format($format);
            }
        } else {
            return null;
        }
    }

    /**
     * Convert string date to [[DateTimeImmutable]] object
     *
     * @param $input \DateTimeInterface|string
     * @param \DateTimeZone|string $tz
     *
     * @return \DateTimeImmutable
     * @noinspection PhpDocMissingThrowsInspection
     */
    final public static function asDateTime($input, $tz = 'UTC')
    {
        $zone = self::getTimeZone($tz);
        if ($input instanceof \DateTimeImmutable) {
            return $input->setTimezone($zone);
        } elseif ($input instanceof \DateTime) {
            return self::cloneDateTime($input)->setTimezone($zone);
        } else {
            /** @noinspection PhpUnhandledExceptionInspection */
            return (new \DateTimeImmutable($input, $zone))->setTimezone($zone);
        }
    }

    /**
     * @param $str
     * @param \DateTimeZone|string $tz
     *
     * @return int
     */
    final public static function asTimestamp($str, $tz = 'UTC')
    {
        return self::asDateTime($str, $tz)->getTimestamp();
    }

    /**
     * @param MongoId $mongoId
     * @param \DateTimeZone|string $tz
     *
     * @return \DateTimeImmutable
     *
     * @noinspection PhpDocMissingThrowsInspection
     */
    final public static function getDateTimeFromMongoId($mongoId, $tz = 'UTC')
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $dateTime = new \DateTimeImmutable('@'.$mongoId->getTimestamp());
        return $dateTime->setTimezone(self::getTimeZone($tz));
    }

    /**
     * @param $str
     * @param \DateTimeZone|string $tz
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
            $msPart = str_pad($parts[1], 6, '0');
        }

        return self::asTimestamp($datePart, $tz) * 1000 + intval(intval($msPart) / 1000);
    }

    /**
     * @param \DateTime $date
     *
     * @return \DateTimeImmutable
     */
    final public static function cloneDateTime($date)
    {
        try {
            $result = new \DateTimeImmutable();
            return $result->setTimezone($date->getTimezone())->setTimestamp($date->getTimestamp());
        } catch (\Exception $e) {
            return null;
        }
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
        try {
            $from = new \DateTimeImmutable();
            $to = $from->add($interval);
            $diff = $to->diff($from, true);
            foreach ($diff as $k => $v) {
                $interval->$k = $v;
            }

        } catch (\Exception $e) {
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
            $timeMarkerSet = true;
            $result .= 'T';
            $result .= sprintf('%dH', $interval->h);
        }

        if ($interval->i !== 0) {
            if (!$timeMarkerSet) {
                $timeMarkerSet = true;
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
     */
    final public static function secondsToSpec($secs)
    {
        if (!empty($secs)) {
            try {
                $interval = self::secondsToInterval($secs);
                return self::intervalToSpec($interval);
            } catch (\Exception $e) {
            }
        }

        return "";
    }

    /**
     * @param string $spec
     *
     * @return int
     *
     * @noinspection PhpDocMissingThrowsInspection
     */
    final public static function specToSeconds($spec)
    {
        if (!empty($spec)) {
            if (is_string($spec) && (($spec[0] == "P") || ($spec[0] == "p"))) {
                $spec = strtoupper($spec);
                /** @noinspection PhpUnhandledExceptionInspection */
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
     */
    final public static function formatSeconds($sec, $parts = 0)
    {
        return self::formatInterval(self::secondsToInterval($sec), $parts);
    }

    /**
     * @param \DateTimeInterface|string $start
     * @param \DateTimeInterface|string|null $end
     * @param int $parts
     *
     * @return string
     */
    final public static function formatDateDiff($start, $end = null, $parts = 0)
    {
        if (!($start instanceof \DateTimeInterface)) {
            $start = self::asDateTime($start);
        }

        if ($end === null) {
            $end = self::now();
        } else {
            if (!($end instanceof \DateTimeInterface)) {
                $end = self::asDateTime($end);
            }
        }

        $interval = $end->diff($start);

        return self::formatInterval($interval, $parts);
    }

    /**
     * @param int $sec
     *
     * @return \DateInterval
     */
    final public static function secondsToInterval($sec)
    {
        try {
            return self::recalculateInterval(new \DateInterval(sprintf('PT%dS', intval($sec))));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param \DateInterval $interval
     *
     * @return int
     */
    final public static function intervalToSeconds($interval)
    {
        try {
            $start = new \DateTimeImmutable();
            $end = $start->add($interval);
            return $end->getTimestamp() - $start->getTimestamp();
        } catch (\Exception $e) {
            return 0;
        }

    }

    /**
     * Convert ISO offset datetime to SQL string
     *
     * @param string $date Datetime i.e. 2011-12-03T10:15:30+01:00
     * @param null $default
     *
     * @return null|string
     */
    final public static function dateIsoOffsetToSqlUtc($date, $default = null)
    {
        if (!empty($date)) {
            return self::asUtcSql($date);
        } else {
            return $default;
        }
    }

    /**
     * Convert ISO offset datetime to SQL string
     *
     * @param string $date Datetime i.e. 2011-12-03 10:15:30
     * @param null $default
     *
     * @return null|string
     */
    final public static function dateSqlUtcToIsoOffset($date, $default = null)
    {
        if (!empty($date)) {
            $date = self::asDateTime($date);
            $date->setTimezone(self::tzUtc());
            return $date->format("Y-m-d\TH:i:sP");
        } else {
            return $default;
        }
    }

    /**
     * Convert Iso8601 datetime to SQL string
     *
     * @param string $date
     * @param string $default
     *
     * @return string
     */
    final public static function dateIso8601ToSqlUtc($date, $default = null)
    {
        if (!empty($date)) {
            return self::asUtcSql($date);
        } else {
            return $default;
        }
    }

    /**
     * Convert SQL datetime to Iso8601 string
     *
     * @param string $date
     * @param string $default
     *
     * @return string
     */
    final public static function dateSqlUtcToIso8601($date, $default = null)
    {
        if (!empty($date)) {
            $date = self::asDateTime($date);
            $date->setTimezone(self::tzUtc());
            return $date->format("Y-m-d\TH:i:s\Z");
        } else {
            return $default;
        }
    }
}
