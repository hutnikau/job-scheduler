# Changelog

## 0.4.1 - 2018-03-20

### Changed
- Removed redundant dependency from composer.json 
 
## 0.4.0 - 2018-03-02

### Added
- RRule::getNextRecurrence(DateTimeInterface $from, $inc = true) method to be able to get time of the next recurrence after given date. 

## 0.3.0 - 2018-02-15

### Added
- Cron syntax support

## 0.2.2 - 2018-01-10

### Added
- .gitattributes file

### Changed
- Improved actions sorting by timestamp

## 0.2.1 - 2018-01-03

### Fixed
- Fix timezone processing in `Job::createFromString()`

## 0.2.0 - 2018-01-03

### Changed
- Wrapped usage of rrule library into \Scheduler\Job\RRule class
 
## 0.1.2 - 2017-12-29

### Added 
- add `Job::createFromString()` factory method

### Changed
- Updated `README.md` file
 
## 0.1.1 - 2017-12-28

### Added 
- PHP 5.6 support

### Changed
- Updated `README.md` file 
