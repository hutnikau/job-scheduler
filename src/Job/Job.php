<?php

namespace Scheduler\Job;

use DateTimeInterface;
use DateTimeZone;
use DateTime;

/**
 * Class Job
 * @package Job
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class Job implements JobInterface
{
    /** @var RRuleInterface */
    private $rRule;

    /** @var callable */
    private $callable;

    /**
     * Job constructor.
     * @param RRuleInterface $rRule - recurrence rule
     * @param callable $callable
     */
    public function __construct(RRuleInterface $rRule, callable $callable)
    {
        $this->rRule = $rRule;
        $this->callable = $callable;
    }

    /**
     * @return RRuleInterface
     */
    public function getRRule()
    {
        return $this->rRule;
    }

    /**
     * @return callable
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * @param string $rRule RRULE string
     * @param string|DateTimeInterface $startDate - @see DateTime supported formats
     * @param callable $callback
     * @param string|DateTimeZone $timezone - If $timezone is omitted, the current timezone will be used.
     *                                        If $startDate is instance of `DateTimeInterface` then $timezone parameter will be ignored
     * @return Job
     */
    public static function createFromString($rRule, $startDate, callable $callback, $timezone = null)
    {
        if ($timezone === null) {
            $timezone = date_default_timezone_get();
        }
        if (is_string($timezone)) {
            $timezone = new DateTimeZone($timezone);
        }
        if (!$startDate instanceof DateTimeInterface) {
            $startDate = new DateTime($startDate, $timezone);
        }
        $rRule = self::createRRule($rRule, $startDate);
        return new self($rRule, $callback);
    }

    /**
     * @param $rRule
     * @param DateTimeInterface $startDate
     * @return RRuleInterface
     */
    protected static function createRRule($rRule, DateTimeInterface $startDate)
    {
        //select implementation
        if (stripos($rRule, 'freq=') !== false) {
            $result = new RRule($rRule, $startDate);
        } else {
            $result = new CronRule($rRule, $startDate);
        }
        return $result;
    }
}