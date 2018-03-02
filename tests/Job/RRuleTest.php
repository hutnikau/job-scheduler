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
        $dt = new DateTime('2017-12-28T21:00:00');

        $rRule = new RRule('FREQ=MINUTELY;', $dt);
        $this->assertEquals(5, count($rRule->getRecurrences($dt, DateTime::createFromFormat('U', $dt->getTimestamp()+(60*4)), true)));
        $this->assertEquals(3, count($rRule->getRecurrences($dt, DateTime::createFromFormat('U', $dt->getTimestamp()+(60*4)), false)));
        $this->assertEquals(1, count($rRule->getRecurrences($dt, $dt, true)));
        $this->assertTrue($rRule->getRecurrences($dt, $dt, true)[0] instanceof DateTimeInterface);
        $this->assertEquals(3,
            count(
                $rRule->getRecurrences(
                    DateTime::createFromFormat('U',  $dt->getTimestamp()-(60*3)),
                    DateTime::createFromFormat('U',  $dt->getTimestamp()+(60*2)),
                    true
                )
            )
        );
        $this->assertEquals(0,
            count(
                $rRule->getRecurrences(
                    DateTime::createFromFormat('U',  $dt->getTimestamp()-(60*10)),
                    DateTime::createFromFormat('U',  $dt->getTimestamp()-(60*1)),
                    true
                )
            )
        );

        $dt = new DateTime('2017-12-28T21:00:01');
        $this->assertEquals(4, count($rRule->getRecurrences($dt, DateTime::createFromFormat('U', $dt->getTimestamp()+(60*4)), true)));

        $rRule = new RRule('FREQ=MINUTELY;COUNT=5', $dt);
        $this->assertEquals(5, count($rRule->getRecurrences($dt, DateTime::createFromFormat('U', $dt->getTimestamp()+(60*20)), true)));
    }

    public function testGetNextRecurrence()
    {
        $dt = new DateTime('2017-12-28T21:00:00');
        $rRule = new RRule('FREQ=MINUTELY;', $dt); //minutely

        $this->assertEquals($dt->getTimestamp(), $rRule->getNextRecurrence($dt)->getTimestamp());
        $this->assertEquals($dt->getTimestamp()+60, $rRule->getNextRecurrence($dt, false)->getTimestamp());

        $dtPlusOneSec = new DateTime('2017-12-28T21:00:01');

        $this->assertEquals($dt->getTimestamp()+60, $rRule->getNextRecurrence($dtPlusOneSec)->getTimestamp());
        $this->assertEquals($dt->getTimestamp()+60, $rRule->getNextRecurrence($dtPlusOneSec, false)->getTimestamp());

        $dtPlusTwoSec = new DateTime('2017-12-28T21:00:02');
        $rRule = new RRule('FREQ=MINUTELY;', $dtPlusTwoSec); //minutely
        $this->assertEquals($dtPlusTwoSec->getTimestamp(), $rRule->getNextRecurrence($dt)->getTimestamp());
        $this->assertEquals($dtPlusTwoSec->getTimestamp(), $rRule->getNextRecurrence($dtPlusTwoSec)->getTimestamp());
        $this->assertEquals($dtPlusTwoSec->getTimestamp()+60, $rRule->getNextRecurrence($dtPlusTwoSec, false)->getTimestamp());

        $this->assertEquals(
            $dtPlusTwoSec->getTimestamp()+120,
            $rRule->getNextRecurrence(new DateTime('@'.($dt->getTimestamp()+120)))->getTimestamp()
        );

        $this->assertEquals(
            $dtPlusTwoSec->getTimestamp()+180,
            $rRule->getNextRecurrence(new DateTime('@'.($dt->getTimestamp()+123)))->getTimestamp()
        );

        $rRule = new RRule('FREQ=MINUTELY;COUNT=5;', $dt); //minutely

        $this->assertEquals(
            $dt->getTimestamp()+(60*4),
            $rRule->getNextRecurrence(new DateTime('@'.($dt->getTimestamp()+(60*4))))->getTimestamp()
        );
        $this->assertEquals(
            null,
            $rRule->getNextRecurrence(new DateTime('@'.($dt->getTimestamp()+(60*5))))
        );
    }
}