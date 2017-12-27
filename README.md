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

``` bash
$ composer require hutnikau/job-scheduler
```

## Usage

### Create a job
``` php
$executionTime = new \DateTime('2017-12-12 20:00:00');
//run monthly, at 20:00:00, 5 times
$rule          = new \Recurr\Rule('FREQ=MONTHLY;COUNT=5', $executionTime);
$job           = new \Scheduler\Job\Job($rule, function () {
    //do something
});
```

###Schedule a job

```php
$scheduler = new \Scheduler\Scheduler([
    $job,
    //more jobs here
]);

//also you may add jobs by \Scheduler\Scheduler::addJob($job)
$scheduler->addJob($anotherJob);
```

### run scheduled jobs 




## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email goodnickoff@gmail.com instead of using the issue tracker.

## Credits

- This library was created by [Aleh Hutnikau](https://github.com/hutnikau)  

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/:vendor/:package_name.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/:vendor/:package_name/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/:vendor/:package_name.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/:vendor/:package_name.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/:vendor/:package_name.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/hutnikau/:package_name
[link-travis]: https://travis-ci.org/hutnikau/:package_name
[link-scrutinizer]: https://scrutinizer-ci.com/g/hutnikau/:package_name/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/hutnikau/:package_name
[link-downloads]: https://packagist.org/packages/hutnikau/:package_name
[link-author]: https://github.com/hutnikau
