<?php
namespace onix\data;

trait SqlDateTimeTrait
{
    private $timeZone = null;

    private $timeZoneName = 'UTC';

    protected function setTimeZoneName($value)
    {
        if ($this->timeZoneName != $value) {
            $this->timeZone = null;
        }

        $this->timeZoneName = $value;
    }

    /**
     * @return \DateTimeZone|null
     * @noinspection PhpDocMissingThrowsInspection
     */
    protected function getTimeZone()
    {
        if ($this->timeZone === null) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->timeZone = new \DateTimeZone($this->timeZoneName);
        }

        return $this->timeZone;
    }

    /**
     * @param \DateTimeZone $value
     * @return void
     */
    protected function setTimeZone(\DateTimeZone $value)
    {
        $this->timeZone = $value;
        $this->timeZoneName = $this->timeZone->getName();
    }

    /**
     * @param $date
     *
     * @param \DateTimeZone|null $tz
     *
     * @return \DateTimeImmutable|false|null
     */
    protected function parseSqlDateTime($date, $tz = null)
    {
        if (!empty($date)) {
            if ($tz === null) {
                $tz = $this->getTimeZone();
            }

            $date = str_replace('T', ' ', $date);

            if (strpos($date, '.') !== false) {
                return \DateTimeImmutable::createFromFormat('Y-m-d H:i:s.u', $date, $tz);
            } elseif (strpos($date, ':') !== false) {
                if (substr_count($date, ':') == 1) {
                    return \DateTimeImmutable::createFromFormat('Y-m-d H:i', $date, $tz);
                } else {
                    return \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $date, $tz);
                }
            } else {
                return \DateTimeImmutable::createFromFormat('Y-m-d', $date, $tz);
            }
        } else {
            return null;
        }
    }

    /**
     * @param string $time
     *
     * @return float|int|null
     */
    protected function parseSqlTimeToSeconds($time)
    {
        if (!empty($time)) {
            $p = explode(':', $time);
            $s = 0.0;
            $m = 1;

            while (count($p) > 0) {
                $s += $m * floatval(array_pop($p));
                $m *= 60;
            }

            return $s;
        } else {
            return null;
        }
    }

    /**
     * @param int|float $time
     * @param int $precision
     *
     * @return false|string
     */
    protected function secondsAsSqlTime($time, $precision = 6)
    {
        $formatted = sprintf("%01.{$precision}f", round($time, $precision));
        @list($integer, $fraction) = explode('.', $formatted);
        $format = $precision == 0
            ? "H:i:s"
            : "H:i:s.".$fraction;

        return gmdate($format, $integer);
    }
}
