<?php

use onix\system\DateInterval;

class DateIntervalTest extends \Codeception\Test\Unit
{
    /**
     * @throws Exception
     */
    public function testTotalYears()
    {
        $interval = new DateInterval("P2Y2M3DT2H5M14S");
        $result = $interval->totalYears();
        $this->assertEquals(2, $result);
    }

    /**
     * @throws Exception
     */
    public function testTotalMonths()
    {
        $interval = new DateInterval("P2Y2M3DT2H5M14S");
        $result = $interval->totalMonths();
        $this->assertEquals(26, $result);
    }

    /**
     * @throws Exception
     */
    public function testTotalDays()
    {
        $interval = new DateInterval("P1M3DT2H5M14S");
        $result = $interval->totalDays();
        print_r($interval);
        $this->assertEquals($result, 34);
    }

    /**
     * @throws Exception
     */
    public function testTotalHours()
    {
        $interval = new DateInterval("P3DT2H5M14S");
        $result = $interval->totalHours();
        $this->assertEquals(74, $result);
    }

    /**
     * @throws Exception
     */
    public function testTotalMinutes()
    {
        $interval = new DateInterval("P3DT2H5M14S");
        $result = $interval->totalMinutes();
        $ex = 3 * 24 * 60 + 2 * 60 + 5;
        $this->assertEquals($ex, $result);
    }

    /**
     * @throws Exception
     */
    public function testTotalSeconds()
    {
        $interval = new DateInterval("P3DT2H5M14S");
        $result = $interval->totalSeconds();
        $ex = (3 * 24 * 60 + 2 * 60 + 5) * 60 + 14;
        $this->assertEquals($ex, $result);
    }
}
