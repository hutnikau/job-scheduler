<?php

namespace Scheduler\Action;

use Scheduler\Exception\SchedulerException;
use Scheduler\Job\JobInterface;
use \DateTimeInterface;
use SuperClosure\Serializer;

/**
 * Class CallableTask
 * @package Task
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class CallableAction implements ActionInterface
{

    /** @var JobInterface */
    private $job;

    /** @var DateTimeInterface */
    private $time;

    /** @var integer */
    private $state;

    /** @var mixed */
    private $report;

    /**
     * @return mixed|void
     */
    public function __invoke()
    {
        $this->state = self::STATE_IN_PROGRESS;
        $this->report = call_user_func($this->getJob()->getCallable(), $this);
        $this->state = self::STATE_FINISHED;
        return $this->report;
    }

    /**
     * AbstractTask constructor.
     * @param JobInterface $job - job related to action
     * @param DateTimeInterface $time - time of occurrence (note: this it not time of execution)
     */
    public function __construct(JobInterface $job, DateTimeInterface $time)
    {
        $this->state = self::STATE_INITIAL;
        $this->job = $job;
        $this->time = $time;
    }

    /**
     * @inheritdoc
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @inheritdoc
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @return mixed|void
     */
    public function getId()
    {
        return $this->getTime()->getTimestamp().md5(implode('_', [
            $this->getTime()->getTimestamp(),
            $this->getJob()->getRRule()->getStartDate()->getTimestamp(),
            $this->getJob()->getRRule()->getRrule(),
            $this->hashCallable($this->getJob()->getCallable())
        ]));
    }

    /**
     * @inheritdoc
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @inheritdoc
     */
    public function getReport()
    {
        if ($this->state !== self::STATE_FINISHED) {
            throw new SchedulerException('Attempt to get report of not finished action');
        }
        return $this->report;
    }

    /**
     * Get unique hash from callable
     * @param $callable
     * @return string
     */
    private function hashCallable($callable)
    {
        $result = '';
        if (is_string($callable)) {
            $result = $callable;
        } else if (is_array($callable)) {
            $callableEntity = array_shift($callable);
            $result = is_object($callableEntity) ? get_class($callableEntity) : serialize($callableEntity);
            $result .= serialize($callable[0]);
        } else if ($callable instanceof \Closure) {
            $serializer = new Serializer();
            return $serializer->serialize($callable);
        } else if (is_object($callable)) {
            $result = serialize($callable);
        }
        return md5($result);
    }
}