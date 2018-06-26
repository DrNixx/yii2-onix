<?php
namespace onix\system;

class DateIntervalHelper
{
    /**
     * @param $interval
     *
     * @return bool|\DateInterval
     */
    public static function normalize($interval)
    {
        $from = new \DateTime("1970-01-01 00:00:00");
        $to = clone $from;
        $to = $to->add($interval);
        $result = $from->diff($to);

        return ($result !== false) ? $result : $interval;
    }

    /**
     * Total years in interval
     *
     * @param \DateInterval $interval
     *
     * @return int
     */
    public static function totalYears($interval)
    {
        return $interval->y;
    }

    /**
     * Total months in interval
     *
     * @param \DateInterval $interval
     *
     * @return int
     */
    public static function totalMonths($interval)
    {
        return $interval->m + $interval->y * 12;
    }

    /**
     * Total days in interval
     *
     * @param \DateInterval $interval
     *
     * @return int
     */
    public static function totalDays($interval)
    {
        if ($interval->days !== false) {
            return $interval->days;
        } else {
            return $interval->d + self::totalMonths($interval) * 30;
        }
    }

    /**
     * Total hours in interval
     *
     * @param \DateInterval $interval
     *
     * @return int
     */
    public static function totalHours($interval)
    {
        return $interval->h + self::totalDays($interval) * 24;
    }

    /**
     * Total minutes in interval
     *
     * @param \DateInterval $interval
     *
     * @return int
     */
    public static function totalMinutes($interval)
    {
        return $interval->i + self::totalHours($interval) * 60;
    }

    /**
     * Total seconds in interval
     *
     * @param \DateInterval $interval
     *
     * @return int
     */
    public static function totalSeconds($interval)
    {
        return $interval->s + self::totalMinutes($interval) * 60;
    }
}