<?php

namespace Scheduler\Job;

use DateTimeInterface;
use Recurr\Rule as RecurrRule;
use Recurr\Recurrence;
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\Constraint\AfterConstraint;
use Recurr\Transformer\Constraint\BetweenConstraint;

/**
 * Class RRule
 * @package Scheduler\Job
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class RRule extends AbstractRule
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
        $result = [];
        $recurrenceCollection = $this->getCollection(new BetweenConstraint($from, $to, $inc));
        /** @var Recurrence $recurrence */
        foreach ($recurrenceCollection as $recurrence) {
            $result[] = $recurrence->getStart();
        }
        return $result;
    }

    /**
     * @param DateTimeInterface $from
     * @param boolean $inc including $from and $to dates
     * @return DateTimeInterface|null date of the next recurrence or null of no more recurrences scheduled.
     * @throws
     */
    public function getNextRecurrence(DateTimeInterface $from, $inc = true)
    {
        $result = null;
        $recurrenceCollection = $this->getCollection(new AfterConstraint($from, $inc));
        if ($first = $recurrenceCollection->first()) {
            $result = $first->getStart();
        }
        return $result;
    }

    /**
     * Get recurrence collection by given constraint
     *
     * @param $constraint
     * @return Recurrence[]|\Recurr\RecurrenceCollection
     * @throws \Recurr\Exception\InvalidRRule
     * @throws \Recurr\Exception\InvalidWeekday
     */
    private function getCollection($constraint)
    {
        $rRule = new RecurrRule($this->getRrule(), $this->getStartDate());
        $rRuleTransformer = new ArrayTransformer();
        return $rRuleTransformer->transform($rRule, $constraint);
    }
}