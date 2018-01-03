<?php

namespace Scheduler\Job;

use DateTimeInterface;
use Recurr\Rule as RecurrRule;
use Recurr\Recurrence;
use Recurr\Transformer\ArrayTransformer;

/**
 * Interface RRule
 * @package Scheduler\Job
 */
class RRule implements RRuleInterface
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
        $rRule = new RecurrRule($this->getRrule(), $this->getStartDate());
        $rRuleTransformer = new ArrayTransformer();
        $recurrenceCollection = $rRuleTransformer->transform($rRule)->startsBetween($from, $to, $inc);
        $result = [];
        /** @var Recurrence $recurrence */
        foreach ($recurrenceCollection as $recurrence) {
            $result[] =$recurrence->getStart();
        }
        return $result;
    }
}