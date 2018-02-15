<?php

namespace Scheduler\Job;

use DateTimeInterface;
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
            $result = $this->getDates($rRule, $from, $to, $inc);
        }

        return $result;
    }

    /**
     * @param CronExpression $rRule
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     * @param $inc
     * @return DateTimeInterface[]
     */
    private function getDates(CronExpression $rRule, DateTimeInterface $from, DateTimeInterface $to, $inc)
    {
        $firstIteration = true;
        $result = [];
        do {
            $nextRunDate = $rRule->getNextRunDate($from, 0, $firstIteration && $inc);
            if ($nextRunDate->getTimestamp() < ($to->getTimestamp() + (int) $inc) && $from->getTimestamp() <= $nextRunDate->getTimestamp()) {
                $result[] = $nextRunDate;
            }
            $firstIteration = false;
            $from = $nextRunDate;
        } while ($nextRunDate->getTimestamp() < ($to->getTimestamp() + (int) $inc));
        return $result;
    }
}