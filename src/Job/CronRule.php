<?php

namespace Scheduler\Job;

use DateTimeInterface;
use DateTime;
use Cron\CronExpression;

/**
 * Class CronRule
 * @package Scheduler\Job
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class CronRule extends AbstractRule
{

    /**
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     * @param boolean $inc
     * @throws
     * @return DateTimeInterface[]
     */
    public function getRecurrences(DateTimeInterface $from, DateTimeInterface $to, $inc = true)
    {
        $rRule = CronExpression::factory($this->getRrule());
        $result = [];

        if ($from->getTimestamp() < $this->getStartDate()->getTimestamp()) {
            $from = clone $this->getStartDate();
        }

        if (($to->getTimestamp() + (int) $inc) > $this->getStartDate()->getTimestamp()) {
            //make sure that $from is DateTime instance
            $from = new DateTime('@'.$from->getTimestamp());
            $result = $this->getDates($rRule, $from, $to, $inc);
        }

        return $result;
    }

    /**
     * @param DateTimeInterface $from
     * @param boolean $inc including $from and $to dates
     * @return DateTimeInterface|null date of the next recurrence or null of no more recurrences scheduled.
     */
    public function getNextRecurrence(DateTimeInterface $from, $inc = true)
    {
        if ($from->getTimestamp() < $this->getStartDate()->getTimestamp()) {
            $from = clone $this->getStartDate();
        }
        //make sure that $from is DateTime instance
        $from = new DateTime('@'.$from->getTimestamp());
        if ($from->format('s') !== '00') {
            $from->setTime($from->format('H'), $from->format('i') + 1, 0);
            $inc = true;
        }

        $rRule = CronExpression::factory($this->getRrule());
        return $rRule->getNextRunDate($from, 0, $inc);
    }

    /**
     * @param CronExpression $rRule
     * @param DateTime $from
     * @param DateTimeInterface $to
     * @param $inc
     * @return DateTimeInterface[]
     */
    private function getDates(CronExpression $rRule, DateTime $from, DateTimeInterface $to, $inc)
    {
        $result = [];
        $toTimestamp = $to->getTimestamp() + (int) $inc;
        do {
            $nextRunDate = $rRule->getNextRunDate($from, 0, $inc);
            $nextRunTimestamp = $nextRunDate->getTimestamp();
            if ($nextRunTimestamp < $toTimestamp && $from->getTimestamp() <= $nextRunTimestamp) {
                $result[] = $nextRunDate;
            }
            $inc = false;
            $from = $nextRunDate;
        } while ($nextRunTimestamp < $toTimestamp);
        return $result;
    }
}