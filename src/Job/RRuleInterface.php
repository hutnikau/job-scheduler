<?php

namespace Scheduler\Job;

use DateTimeInterface;

/**
 * Interface RRule
 * @package Scheduler\Job
 */
interface RRuleInterface
{

    /**
     * @return DateTimeInterface
     */
    public function getStartDate();

    /**
     * @return string RRULE string
     */
    public function getRrule();

    /**
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     * @param boolean $inc
     * @return DateTimeInterface[]
     */
    public function getRecurrences(DateTimeInterface $from, DateTimeInterface $to, $inc = true);
}