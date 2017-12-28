<?php

namespace Scheduler\Action;

use Scheduler\Job\JobInterface;
use \DateTimeInterface;

/**
 * Class CallableTask
 * @package Task
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
class CallableAction implements ActionInterface
{

    /** @var JobInterface  */
    private $job;

    /** @var DateTimeInterface  */
    private $time;

    /**
     * @return mixed|void
     */
    public function __invoke()
    {
        return call_user_func($this->getJob()->getCallable());
    }

    /**
     * AbstractTask constructor.
     * @param JobInterface $job - job related to action
     * @param DateTimeInterface $time - time of occurrence (note: this it not time of execution)
     */
    public function __construct(JobInterface $job, DateTimeInterface $time)
    {
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
}