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

    /**
     * @throws Exception
     */
    public function testAsUtcSql()
    {
        $s = DateTimeHelper::asUtcSql("2013-09-29T18:46:19Z");
        $this->assertEquals("2013-09-29 18:46:19", $s);

        $s = DateTimeHelper::asUtcSql("2013-09-29T18:46:19GMT");
        $this->assertEquals("2013-09-29 18:46:19", $s);

        $s = DateTimeHelper::asUtcSql("2010-07-05 08:00:00+0200");
        $this->assertEquals("2010-07-05 06:00:00", $s);

        $s = DateTimeHelper::asUtcSql("2010-07-05 08:00:00 MSK");
        $this->assertEquals("2010-07-05 05:00:00", $s);

        $s = DateTimeHelper::asUtcSql("2010-07-05T08:00:00MSK");
        $this->assertEquals("2010-07-05 05:00:00", $s);

        $s = DateTimeHelper::asUtcSql("2010-07-05 08:00:00");
        $this->assertEquals("2010-07-05 08:00:00", $s);
    }

    /**
     * @throws Exception
     */
    public function testAsSql()
    {
        $s = DateTimeHelper::asSql("2013-09-29T18:46:19Z");
        $this->assertEquals("2013-09-29 18:46:19+0000", $s);

        $s = DateTimeHelper::asSql("2010-07-05 08:00:00+0200");
        $this->assertEquals("2010-07-05 06:00:00+0000", $s);

        $s = DateTimeHelper::asSql("2010-07-05 08:00:00 MSK");
        $this->assertEquals("2010-07-05 05:00:00+0000", $s);

        $s = DateTimeHelper::asSql("2010-07-05 08:00:00");
        $this->assertEquals("2010-07-05 08:00:00+0000", $s);
    }

    /**
     * @throws Exception
     */
    public function testAsDateTime()
    {
        $d = DateTimeHelper::asDateTime("2013-09-29T18:46:19Z");
        $this->assertInstanceOf("DateTimeImmutable", $d);

        $this->assertEquals("2013-09-29 18:46:19", $d->format("Y-m-d H:i:s"));

        $d = DateTimeHelper::asDateTime("2010-07-05 08:00:00+0200", "Europe/Amsterdam");
        $this->assertEquals("2010-07-05 08:00:00+0200", $d->format("Y-m-d H:i:sO"));

        $d = DateTimeHelper::asDateTime("2010-07-05 08:00:00 MSK");
        $this->assertEquals("2010-07-05 05:00:00+0000", $d->format("Y-m-d H:i:sO"));

        $d = DateTimeHelper::asDateTime("2010-07-05 08:00:00");
        $this->assertEquals("2010-07-05 08:00:00+0000", $d->format("Y-m-d H:i:sO"));
    }

    /**
     * @throws Exception
     */
    public function testAsTimestamp()
    {
        $t = DateTimeHelper::asTimestamp('2016-03-11 11:00:00', "Europe/Rome");
        $this->assertEquals(1457690400, $t);
        $t = DateTimeHelper::asTimestamp('2016-03-11 11:00:00', "America/New_York");
        $this->assertEquals(1457712000, $t);
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
        $s = 172800;
        $i = DateTimeHelper::secondsToInterval($s);
        $this->assertEquals(0, $i->y);
        $this->assertEquals(0, $i->m);
        $this->assertEquals(2, $i->d);
        $this->assertEquals(0, $i->h);
        $this->assertEquals(0, $i->i);
        $this->assertEquals(0, $i->s);

        $s = 2;
        $i = DateTimeHelper::secondsToInterval($s);
        $this->assertEquals(0, $i->y);
        $this->assertEquals(0, $i->m);
        $this->assertEquals(0, $i->d);
        $this->assertEquals(0, $i->h);
        $this->assertEquals(0, $i->i);
        $this->assertEquals(2, $i->s);
    }

    /**
     * @throws Exception
     */
    public function testIntervalToSeconds()
    {
        $i = new \DateInterval("P2D");
        $s = DateTimeHelper::intervalToSeconds($i);
        $this->assertEquals(172800, $s);

        $i = new \DateInterval("PT2S");
        $s = DateTimeHelper::intervalToSeconds($i);
        $this->assertEquals(2, $s);
    }

    /**
     * @throws Exception
     */
    public function testDateIsoOffsetToSqlUtc()
    {
        $s = DateTimeHelper::dateIsoOffsetToSqlUtc("2013-09-29T18:46:19+02:00");
        $this->assertEquals("2013-09-29 16:46:19", $s);
    }

    /**
     * @throws Exception
     */
    public function testDateSqlUtcToIsoOffset()
    {
        $s = DateTimeHelper::dateSqlUtcToIsoOffset("2013-09-29 18:46:19");
        $this->assertEquals("2013-09-29T18:46:19+00:00", $s);
    }

    /**
     * @throws Exception
     */
    public function testDateIso8601ToSqlUtc()
    {
        $s = DateTimeHelper::dateIso8601ToSqlUtc("2013-09-29T18:46:19Z");
        $this->assertEquals("2013-09-29 18:46:19", $s);
    }

    /**
     * @throws Exception
     */
    public function testDateSqlUtcToIso8601()
    {
        $s = DateTimeHelper::dateSqlUtcToIso8601("2013-09-29 18:46:19");
        $this->assertEquals("2013-09-29T18:46:19Z", $s);
    }
}
