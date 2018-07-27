<?php

namespace Scheduler\Worker;

use DateInterval;
use DateTime;
use Scheduler\Exception\SchedulerException;
use Scheduler\JobRunner\JobRunnerInterface;
use Scheduler\SchedulerInterface;

/**
 * Class AbstractWorker
 * @package Scheduler\Worker
 * @author Aleh Hutnikau, <goodnickoff@gmail.com>
 */
abstract class AbstractWorker implements WorkerInterface
{
    const DEFAULT_ITERATION_INTERVAL = 'PT60S';

    /** @var DateTime */
    private $from;

    /** @var DateInterval */
    private $interval;

    /** @var bool */
    private $shutdown = false;

    /** @var integer */
    private $iteration = 0;

    /** @var integer */
    private $maxIterations = 1000;

    /**
     * @param integer $startTime
     * @param string $interval
     * @return mixed|string
     * @throws SchedulerException
     */
    public function run($startTime, $interval)
    {
        $this->init([$startTime, $interval]);
        $jobRunner = $this->getJobRunner();

        $from = clone($this->from);

        while ($this->isRunning()) {
            $to = new DateTime('now', new \DateTimeZone('UTC'));
            $jobRunner->run($this->getScheduler(), $from, $to, true);
            $from = clone($to);
            sleep($this->getSeconds($this->interval));
            $this->iteration++;
        }

        return 'Shut down scheduler worker';
    }

    /**
     * @return JobRunnerInterface
     */
    abstract protected function getJobRunner();

    /**
     * @return SchedulerInterface
     */
    abstract protected function getScheduler();

    /**
     * @return bool
     */
    public function isRunning()
    {
        pcntl_signal_dispatch();

        if ($this->iteration >= $this->getMaxIterations()) {
            $this->shutdown();
        }

        if ($this->shutdown) {
            return false;
        }

        return true;
    }

    /**
     * Set marker to shutdown after finishing current iteration
     */
    public function shutdown()
    {
        $this->shutdown = true;
    }

    /**
     * @param $iterations
     * @throws SchedulerException
     * @return mixed|void
     */
    public function setMaxIterations($iterations)
    {
        if (!is_integer($iterations)) {
            throw new SchedulerException('$iterations parameter must be integer');
        }
        $this->maxIterations = $iterations;
    }

    /**
     * Get amount of seconds in date interval
     *
     * @param DateInterval $interval
     * @return int
     * @throws \Exception
     */
    private function getSeconds(DateInterval $interval)
    {
        $date = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $date2 = $date->add($interval);
        return $date2->getTimestamp() - $date->getTimestamp();
    }

    /**
     * @param array $params
     * @throws
     */
    private function init($params = [])
    {
        if (!isset($params[0]) || !is_numeric($params[0])) {
            throw new SchedulerException('Start time parameter must be numeric');
        }
        $this->from = new DateTime('@' . $params[0], new \DateTimeZone('UTC'));

        $this->interval = new \DateInterval(self::DEFAULT_ITERATION_INTERVAL);
        if (isset($params[1])) {
            $this->interval = new \DateInterval($params[1]);
        }

        $this->registerSigHandlers();
    }

    /**
     * Register signal handlers that a worker should respond to.
     *
     * TERM/INT/QUIT: Shutdown after the current job is finished then exit.
     */
    private function registerSigHandlers()
    {
        if (!function_exists('pcntl_signal')) {
            //worker ran without pcntl
            function pcntl_signal() {
                //do nothing
            }
            define('SIGTERM', 15);
            define('SIGINT', 2);
            define('SIGQUIT', 3);
        }
        if (!function_exists('pcntl_signal_dispatch')) {
            function pcntl_signal_dispatch() {
                //do nothing
            };
        }

        declare(ticks = 1);

        pcntl_signal(SIGTERM, [$this, 'shutdown']);
        pcntl_signal(SIGINT, [$this, 'shutdown']);
        pcntl_signal(SIGQUIT, [$this, 'shutdown']);
    }

    /**
     * @return integer
     */
    public function getMaxIterations()
    {
        return $this->maxIterations;
    }
}