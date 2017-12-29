<?php

namespace Scheduler\Job;

use Recurr\Rule;
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
    /** @var Rule */
    private $rRule;

    /** @var callable */
    private $callable;

    /**
     * Job constructor.
     * @param Rule $rRule - recurrence rules (@see https://github.com/simshaun/recurr)
     * @param callable $callable
     */
    public function __construct(Rule $rRule, callable $callable)
    {
        $this->rRule = $rRule;
        $this->callable = $callable;
    }

    /**
     * @return Rule
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
     * @return Job
     */
    public static function createFromString($rRule, $startDate, callable $callback, $timezone = null)
    {
        if (!$startDate instanceof DateTimeInterface) {
            $startDate = new DateTime($startDate, new \DateTimeZone($timezone));
        }
        if (empty($timezone)) {
            $timezone = $startDate->getTimezone()->getName();
        }
        $rRule = new Rule($rRule, $startDate, null, $timezone);
        return new self($rRule, $callback);
    }
}