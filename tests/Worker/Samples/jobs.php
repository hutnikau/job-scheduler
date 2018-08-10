<?php

namespace SchedulerTests\Worker\Samples;

use Scheduler\Job\RRule;
use Scheduler\Job\Job;
use Scheduler\Action\ActionInterface;

/** @var int $start */

$rule = new RRule('FREQ=MINUTELY;COUNT=5', \DateTime::createFromFormat('U', $start, new \DateTimeZone('UTC')));
$rule2 = new RRule('FREQ=HOURLY;COUNT=5', \DateTime::createFromFormat('U', $start, new \DateTimeZone('UTC')));
$rule3 = new RRule('FREQ=MINUTELY;COUNT=100', \DateTime::createFromFormat('U', $start, new \DateTimeZone('UTC')));
$rule4 = new RRule('FREQ=HOURLY;COUNT=100', \DateTime::createFromFormat('U', $start, new \DateTimeZone('UTC')));

$jobs = [
    new Job($rule, function (ActionInterface $action) {
        ActionClass::log($action->getId());
    }),
    new Job($rule, '\SchedulerTests\Worker\Samples\my_callback_function'),
    new Job($rule, [new ActionClass(), 'myCallbackMethod']),
    new Job($rule, ['\SchedulerTests\Worker\Samples\ActionClass', 'myCallbackMethod2']),
    new Job($rule, new ActionClass()),

    new Job($rule2, function (ActionInterface $action) {
        ActionClass::log($action->getId());
    }),
    new Job($rule2, '\SchedulerTests\Worker\Samples\my_callback_function'),
    new Job($rule2, [new ActionClass(), 'myCallbackMethod']),
    new Job($rule2, ['\SchedulerTests\Worker\Samples\ActionClass', 'myCallbackMethod2']),
    new Job($rule2, new ActionClass()),

    new Job($rule3, function (ActionInterface $action) {
        ActionClass::log($action->getId());
    }),
    new Job($rule3, '\SchedulerTests\Worker\Samples\my_callback_function'),
    new Job($rule3, [new ActionClass(), 'myCallbackMethod']),
    new Job($rule3, ['\SchedulerTests\Worker\Samples\ActionClass', 'myCallbackMethod2']),
    new Job($rule3, new ActionClass()),

    new Job($rule4, function (ActionInterface $action) {
        ActionClass::log($action->getId());
    }),
    new Job($rule4, '\SchedulerTests\Worker\Samples\my_callback_function'),
    new Job($rule4, [new ActionClass(), 'myCallbackMethod']),
    new Job($rule4, ['\SchedulerTests\Worker\Samples\ActionClass', 'myCallbackMethod2']),
    new Job($rule4, new ActionClass()),
];

function my_callback_function(ActionInterface $action) {
    ActionClass::log($action->getId());
}
class ActionClass {
    static function myCallbackMethod(ActionInterface $action) {
        ActionClass::log($action->getId());
    }
    static function myCallbackMethod2(ActionInterface $action) {
        ActionClass::log($action->getId());
    }
    public function __invoke(ActionInterface $action) {
        ActionClass::log($action->getId());
    }
    static function log($message)
    {
        $f = fopen(__DIR__ . DIRECTORY_SEPARATOR . 'report', 'a');
        if (flock($f, LOCK_EX)) {
            fwrite($f, $message . PHP_EOL);
            flock($f, LOCK_UN);
        } else {
            throw new \Exception('can\' lock file');
        }
        fclose($f);
    }
}