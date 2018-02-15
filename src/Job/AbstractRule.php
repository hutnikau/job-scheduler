<?php

namespace Scheduler\Job;

use DateTimeInterface;

/**
 * Class AbstractRule
 * @package Scheduler\Job
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
abstract class AbstractRule implements RRuleInterface
{
    /** @var DateTimeInterface|string  */
    protected $startDate;

    /** @var string  */
    protected $rRule;

    /**
     * Rule constructor.
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
     * @return string recurrence rule string
     */
    public function getRrule()
    {
        return $this->rRule;
    }

    /**
     * @param DateTimeInterface $from
     * @param DateTimeInterface $to
     * @param boolean $inc
     * @return DateTimeInterface[]
     */
    abstract public function getRecurrences(DateTimeInterface $from, DateTimeInterface $to, $inc = true);
}