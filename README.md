# job-scheduler

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Job scheduler is a PHP library for scheduling time-based repetitive actions.
It uses ([RRULE](https://tools.ietf.org/html/rfc5545)) to configure time and recurrence rule of each job.  

## Goals

Sometimes amount of cron jobs becomes too large. 
The main goal is reduce amount of cron jobs to only one.  

## Installation

Via Composer

```bash
$ composer require hutnikau/job-scheduler
```

## Usage

### Create a job
Job constructor have the following signature:
`\Scheduler\Job\Job::__construct(Rule $rRule, callable $callable);`

Example:
```php
$executionTime = new \DateTime('2017-12-12 20:00:00');
//run monthly, at 20:00:00, 5 times
$rule          = new \Recurr\Rule('FREQ=MONTHLY;COUNT=5', $executionTime);
$job           = new \Scheduler\Job\Job($rule, function () {
    //do something
});
```

Here you may find more information about recurring rules:
https://github.com/simshaun/recurr

### Schedule a job

Scheduler constructor accepts array of jobs as first parameter:

```php
$scheduler = new \Scheduler\Scheduler([
    $job,
    //more jobs here
]);

//also you may add jobs by `\Scheduler\Scheduler::addJob($job)`
$scheduler->addJob($anotherJob);
```

### Run scheduled jobs 

Run all jobs scheduled from '2017-12-12 20:00:00' to '2017-12-12 20:10:00':

```php
$jobRunner = new \Scheduler\JobRunner\JobRunner();
$from      = new \DateTime('2017-12-12 20:00:00');
$to        = new \DateTime('2017-12-12 20:10:00');
$reports   = $jobRunner->run($scheduler, $from, $to, true);
```

> Note: the last `true` parameter means that jobs scheduled exactly at `from` or `to` time will be included.
> In this example it means that jobs scheduled to be run at '2017-12-12 20:00:00' or '2017-12-12 20:10:00' will be executed.

`$jobRunner->run(...)` returns an array of reports (\Scheduler\Action\Report)

### Reports

`\Scheduler\Action\Report` class synopsis: 

```
\Scheduler\Action\Report {
    /* Methods */
    public mixed getReport ( void )
    public mixed getAction ( void )
    public mixed getType ( void )
}
```

In case if during execution an exception has been thrown then this exception will be returned as a result of action.

`$report->getType()` returns one of two values: `\Scheduler\Action\Report::TYPE_SUCCESS | \Scheduler\Action\Report::TYPE_ERROR`
  

## Warnings

1. Be careful with timezones. Make sure that you create `\DateTime` instances with correct timezone.
2. Accuracy of scheduler up to seconds. You must be accurate with `$from`, `$to` parameters passed to the runner to not miss an action or not launch an action twice.   

## Testing

```bash
$ composer test
```

## Security

If you discover any security related issues, please email goodnickoff@gmail.com instead of using the issue tracker.

## Credits

- This library was created by [Aleh Hutnikau](https://github.com/hutnikau)  

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/hutnikau/job-scheduler.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/hutnikau/job-scheduler/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/hutnikau/job-scheduler.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/hutnikau/job-scheduler.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/hutnikau/job-scheduler.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/hutnikau/job-scheduler
[link-travis]: https://travis-ci.org/hutnikau/job-scheduler
[link-scrutinizer]: https://scrutinizer-ci.com/g/hutnikau/job-scheduler/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/hutnikau/job-scheduler
[link-downloads]: https://packagist.org/packages/hutnikau/job-scheduler
[link-author]: https://github.com/hutnikau
