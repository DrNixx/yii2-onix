<?php
/**
 * Created by PhpStorm.
 * User: npanteleev
 * Date: 13.03.2018
 * Time: 18:21
 */

use onix\system\DateTimeHelper;

class DateTimeHelperTest extends \Codeception\Test\Unit
{

    public function testMinDate()
    {
        $md = DateTimeHelper::minDate();
        $str = $md->format("Y-m-d H:i:s");
        $this->assertEquals("1900-01-01 00:00:00", $str);
    }

    public function testNow()
    {
        $str = gmdate("Y-m-d H:i");
        $now = DateTimeHelper::now();
        $this->assertEquals($str, $now->format("Y-m-d H:i"));
    }

    public function testNowSql()
    {
        $str = gmdate("Y-m-d H:i:s");
        $now = DateTimeHelper::nowSql();
        $this->assertEquals($str, $now);
    }

    public function testDateOrNowSql()
    {
        $now = DateTimeHelper::now();
        $str = $now->format("Y-m-d H:i");
        $test = DateTimeHelper::dateOrNowSql($str);
        $this->assertEquals($str, $test);

        $str = gmdate("Y-m-d H:i:s");
        $now = DateTimeHelper::dateOrNowSql(null);
        $this->assertEquals($str, $now);
    }

    public function testAsSql()
    {

    }

    public function testAsDateTime()
    {

    }

    public function testAsTimestamp()
    {

    }

    public function testGetDateTimeFromMongoId()
    {

    }

    public function testAsJavaScriptTimestamp()
    {

    }

    public function testCloneDateTime()
    {

    }

    public function testTzOffsetToName()
    {

    }

    public function testRecalculateInterval()
    {

    }

    public function testIntervalToSpec()
    {

    }

    public function testSecondsToSpec()
    {

    }

    public function testSpecToSeconds()
    {

    }

    public function testFormatInterval()
    {

    }

    public function testFormatSeconds()
    {

    }

    public function testFormatDateDiff()
    {

    }

    public function testSecondsToInterval()
    {

    }

    public function testIntervalToSeconds()
    {

    }

    public function testDateIso8601ToSqlWithoutTZ()
    {

    }

    public function testDateSqlToIso8601To()
    {

    }
}
