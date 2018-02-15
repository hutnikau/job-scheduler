<?php

namespace Scheduler\Job;

use DateTimeInterface;
use Cron\CronExpression;

/**
 * Class CronRule
 * @package Scheduler\Job
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class CronRule implements RRuleInterface
{

    private $startDate;
    private $rRule;

    /**
     * RRule constructor.
     * @param string $rRule
     * @param string|DateTimeInterface $startDate
     */
    public function __construct($rRule, DateTimeInterface $startDate)
    {
        $this->rRule = $rRule;
        $this->startDate = $startDate;
    }

    /**
     * @return DateTimeInterface
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return string RRULE string
     */
    public function getRrule()
    {
        return $this->rRule;
    }

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

        if ($to->getTimestamp() < $this->getStartDate()->getTimestamp()) {
            return $result;
        }

        if ($from->getTimestamp() < $this->getStartDate()->getTimestamp()) {
            $from = clone $this->getStartDate();
        }

        $firstIteration = true;

        do {
            $nextRunDate = $rRule->getNextRunDate($from, 0, $firstIteration && $inc);
            if ($nextRunDate->getTimestamp() < ($to->getTimestamp() + (int) $inc) && $from->getTimestamp() <= $nextRunDate->getTimestamp()) {
                $result[] = $nextRunDate;
            }
            $firstIteration = false;
            $from = $nextRunDate;
        } while ($nextRunDate->getTimestamp() < ($to->getTimestamp() + (int) $inc));

        return $result;


//        while(($nextRunDate = $rRule->getNextRunDate($from, 0, $inc)->getTimestamp()) < ($to->getTimestamp() + (int) $inc)) {
//            $nextRunDate = $rRule->getNextRunDate($from, 0, $inc);
//            $result[] = $recurrence->getStart()
//        }

//        $rRule = new RecurrRule($this->getRrule(), $this->getStartDate());
//        $rRuleTransformer = new ArrayTransformer();
//        $constraint = new BetweenConstraint($from, $to, $inc);
//        $recurrenceCollection = $rRuleTransformer->transform($rRule, $constraint);
//        $result = [];
//        /** @var Recurrence $recurrence */
//        foreach ($recurrenceCollection as $recurrence) {
//            $result[] = $recurrence->getStart();
//        }
//        return $result;
    }
}