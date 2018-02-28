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
     * @param boolean $inc including $from and $to dates
     * @return DateTimeInterface[]
     */
    public function getRecurrences(DateTimeInterface $from, DateTimeInterface $to, $inc = true);

    /**
     * @param DateTimeInterface $from
     * @param boolean $inc including $from and $to dates
     * @return DateTimeInterface|null date of the next recurrence or null of no more recurrences scheduled.
     */
    public function getNextRecurrence(DateTimeInterface $from, $inc = true);
}