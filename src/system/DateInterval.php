<?php
namespace onix\system;

class DateInterval extends \DateInterval
{
    /**
     * @var int
     */
    private $days2;

    /**
     * Interval has been normalized
     * @var bool
     */
    private $normalized = false;

    private function normalize()
    {
        if (!$this->normalized) {
            $from = new \DateTime("2000-01-01 00:00:00");
            $to = clone $from;
            $to = $to->add($this);
            $diff = $from->diff($to);
            $this->days2 = $diff->days;
            foreach ($diff as $k => $v) {
                $this->$k = $v;
            }
        }

        return $this;
    }

    /**
     * @return int
     */
    public function totalSeconds()
    {
        $this->normalize();
        return $this->s + $this->totalMinutes() * 60;
    }

    /**
     * @return int
     */
    public function totalMinutes()
    {
        $this->normalize();
        return $this->i + $this->totalHours() * 60;
    }

    /**
     * @return int
     */
    public function totalHours()
    {
        $this->normalize();
        return $this->h + $this->totalDays() * 24;
    }

    /**
     * @return int
     */
    public function totalDays()
    {
        if ($this->days) {
            return $this->days;
        }

        $this->normalize();
        return $this->days2;
    }

    /**
     * Total months in interval
     *
     * @return int
     */
    public function totalMonths()
    {
        $this->normalize();

        return $this->m + $this->y * 12;
    }

    /**
     * Total years in interval
     *
     * @return int
     */
    public function totalYears()
    {
        $this->normalize();
        return $this->y;
    }
}
