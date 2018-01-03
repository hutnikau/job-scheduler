<?php

namespace SchedulerTests\Job;

use PHPUnit\Framework\TestCase;
use DateTimeInterface;
use DateTime;
use Scheduler\Job\RRule;

class RRuleTest extends TestCase
{
    public function testGetStartDate()
    {
        $dt = new DateTime('2017-12-28T21:00:00');
        $rRule = new RRule('FREQ=MONTHLY;COUNT=5', $dt);
        $this->assertEquals($dt, $rRule->getStartDate());
    }

    public function testGetRrule()
    {
        $dt = new DateTime('2017-12-28T21:00:00');
        $rRule = new RRule('FREQ=MONTHLY;COUNT=5', $dt);
        $this->assertEquals('FREQ=MONTHLY;COUNT=5', $rRule->getRrule());
    }

    public function testGetRecurrences()
    {
        $now = time();
        $dt = DateTime::createFromFormat('U', $now);
        $rRule = new RRule('FREQ=MINUTELY;COUNT=5', $dt);
        $this->assertEquals(5, count($rRule->getRecurrences($dt, DateTime::createFromFormat('U', $now+(60*4)), true)));
        $this->assertEquals(3, count($rRule->getRecurrences($dt, DateTime::createFromFormat('U', $now+(60*4)), false)));
        $this->assertEquals(1, count($rRule->getRecurrences($dt, $dt, true)));
        $this->assertTrue($rRule->getRecurrences($dt, $dt, true)[0] instanceof DateTimeInterface);
    }
}