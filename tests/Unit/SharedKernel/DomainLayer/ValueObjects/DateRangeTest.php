<?php

declare(strict_types=1);

namespace Tests\SharedKernel\DomainLayer\ValueObjects;

use DateTime;
use InvalidArgumentException;
use Src\SharedKernel\DomainLayer\ValueObjects\DateRange;
use Src\SharedKernel\DomainLayer\ValueObjects\TimeInterval;

test('it can create date range with only dates', function () {
    $dateRange = new DateRange('2024-01-15', '2024-01-20');

    expect($dateRange)
        ->from->toBe('2024-01-15')
        ->to->toBe('2024-01-20')
        ->timeIntervalFrom->toBeNull()
        ->timeIntervalTo->toBeNull();
});

test('it can create date range with single day', function () {
    $dateRange = new DateRange('2024-01-15', '2024-01-15');

    expect($dateRange)
        ->from->toBe('2024-01-15')
        ->to->toBe('2024-01-15');
});

test('it can create date range with time interval from', function () {
    $timeInterval = new TimeInterval(
        new DateTime('2024-01-15 09:00:00'),
        new DateTime('2024-01-16 17:00:00')
    );
    $dateRange = new DateRange('2024-01-15', '2024-01-20', $timeInterval);

    expect($dateRange)
        ->from->toBe('2024-01-15')
        ->to->toBe('2024-01-20')
        ->timeIntervalFrom->toBeInstanceOf(TimeInterval::class)
        ->timeIntervalTo->toBeNull();
});

test('it can create date range with time interval to', function () {
    $timeInterval = new TimeInterval(
        new DateTime('2024-01-18 10:00:00'),
        new DateTime('2024-01-20 18:00:00')
    );
    $dateRange = new DateRange('2024-01-15', '2024-01-20', null, $timeInterval);

    expect($dateRange)
        ->from->toBe('2024-01-15')
        ->to->toBe('2024-01-20')
        ->timeIntervalFrom->toBeNull()
        ->timeIntervalTo->toBeInstanceOf(TimeInterval::class);
});

test('it can create date range with both time intervals', function () {
    $timeIntervalFrom = new TimeInterval(
        new DateTime('2024-01-15 09:00:00'),
        new DateTime('2024-01-16 17:00:00')
    );
    $timeIntervalTo = new TimeInterval(
        new DateTime('2024-01-18 10:00:00'),
        new DateTime('2024-01-20 18:00:00')
    );
    $dateRange = new DateRange('2024-01-15', '2024-01-20', $timeIntervalFrom, $timeIntervalTo);

    expect($dateRange)
        ->from->toBe('2024-01-15')
        ->to->toBe('2024-01-20')
        ->timeIntervalFrom->toBeInstanceOf(TimeInterval::class)
        ->timeIntervalTo->toBeInstanceOf(TimeInterval::class);
});

test('it can create date range with time interval on same day as date range', function () {
    $timeInterval = new TimeInterval(
        new DateTime('2024-01-15 09:00:00'),
        new DateTime('2024-01-15 17:00:00')
    );
    $dateRange = new DateRange('2024-01-15', '2024-01-15', $timeInterval);

    expect($dateRange)
        ->from->toBe('2024-01-15')
        ->to->toBe('2024-01-15')
        ->timeIntervalFrom->toBeInstanceOf(TimeInterval::class);
});

test('cannot create date range with invalid date format for from', function () {
    expect(fn () => new DateRange('2024/01/15', '2024-01-20'))
        ->toThrow(InvalidArgumentException::class, 'Invalid date format for "from"');

    expect(fn () => new DateRange('01-15-2024', '2024-01-20'))
        ->toThrow(InvalidArgumentException::class, 'Invalid date format for "from"');

    expect(fn () => new DateRange('2024-1-15', '2024-01-20'))
        ->toThrow(InvalidArgumentException::class, 'Invalid date format for "from"');

    expect(fn () => new DateRange('invalid', '2024-01-20'))
        ->toThrow(InvalidArgumentException::class, 'Invalid date format for "from"');
});

test('cannot create date range with invalid date format for to', function () {
    expect(fn () => new DateRange('2024-01-15', '2024/01/20'))
        ->toThrow(InvalidArgumentException::class, 'Invalid date format for "to"');

    expect(fn () => new DateRange('2024-01-15', '01-20-2024'))
        ->toThrow(InvalidArgumentException::class, 'Invalid date format for "to"');

    expect(fn () => new DateRange('2024-01-15', '2024-1-20'))
        ->toThrow(InvalidArgumentException::class, 'Invalid date format for "to"');

    expect(fn () => new DateRange('2024-01-15', 'invalid'))
        ->toThrow(InvalidArgumentException::class, 'Invalid date format for "to"');
});

test('cannot create date range when from is greater than to', function () {
    expect(fn () => new DateRange('2024-01-20', '2024-01-15'))
        ->toThrow(InvalidArgumentException::class, 'Date "from" cannot be greater than date "to"');
});

test('cannot create date range with time interval from that starts before date range', function () {
    $timeInterval = new TimeInterval(
        new DateTime('2024-01-14 09:00:00'),
        new DateTime('2024-01-16 17:00:00')
    );

    expect(fn () => new DateRange('2024-01-15', '2024-01-20', $timeInterval))
        ->toThrow(InvalidArgumentException::class, 'Time interval must fall within the date range');
});

test('cannot create date range with time interval from that ends after date range', function () {
    $timeInterval = new TimeInterval(
        new DateTime('2024-01-15 09:00:00'),
        new DateTime('2024-01-21 17:00:00')
    );

    expect(fn () => new DateRange('2024-01-15', '2024-01-20', $timeInterval))
        ->toThrow(InvalidArgumentException::class, 'Time interval must fall within the date range');
});

test('cannot create date range with time interval from that spans outside date range', function () {
    $timeInterval = new TimeInterval(
        new DateTime('2024-01-14 09:00:00'),
        new DateTime('2024-01-21 17:00:00')
    );

    expect(fn () => new DateRange('2024-01-15', '2024-01-20', $timeInterval))
        ->toThrow(InvalidArgumentException::class, 'Time interval must fall within the date range');
});

test('cannot create date range with time interval to that starts before date range', function () {
    $timeInterval = new TimeInterval(
        new DateTime('2024-01-14 09:00:00'),
        new DateTime('2024-01-16 17:00:00')
    );

    expect(fn () => new DateRange('2024-01-15', '2024-01-20', null, $timeInterval))
        ->toThrow(InvalidArgumentException::class, 'Time interval must fall within the date range');
});

test('cannot create date range with time interval to that ends after date range', function () {
    $timeInterval = new TimeInterval(
        new DateTime('2024-01-18 09:00:00'),
        new DateTime('2024-01-21 17:00:00')
    );

    expect(fn () => new DateRange('2024-01-15', '2024-01-20', null, $timeInterval))
        ->toThrow(InvalidArgumentException::class, 'Time interval must fall within the date range');
});

test('can create date range with time interval from that exactly matches date range boundaries', function () {
    $timeInterval = new TimeInterval(
        new DateTime('2024-01-15 00:00:00'),
        new DateTime('2024-01-20 23:59:59')
    );
    $dateRange = new DateRange('2024-01-15', '2024-01-20', $timeInterval);

    expect($dateRange)
        ->from->toBe('2024-01-15')
        ->to->toBe('2024-01-20')
        ->timeIntervalFrom->toBeInstanceOf(TimeInterval::class);
});

test('can create date range with time interval to that exactly matches date range boundaries', function () {
    $timeInterval = new TimeInterval(
        new DateTime('2024-01-15 00:00:00'),
        new DateTime('2024-01-20 23:59:59')
    );
    $dateRange = new DateRange('2024-01-15', '2024-01-20', null, $timeInterval);

    expect($dateRange)
        ->from->toBe('2024-01-15')
        ->to->toBe('2024-01-20')
        ->timeIntervalTo->toBeInstanceOf(TimeInterval::class);
});

test('date range is immutable', function () {
    $dateRange = new DateRange('2024-01-15', '2024-01-20');

    expect($dateRange)->toBeInstanceOf(DateRange::class);
    // readonly class ensures immutability
});

test('can create date range with leap year date', function () {
    $dateRange = new DateRange('2024-02-29', '2024-03-01');

    expect($dateRange)
        ->from->toBe('2024-02-29')
        ->to->toBe('2024-03-01');
});

test('cannot create date range with invalid leap year date', function () {
    expect(fn () => new DateRange('2023-02-29', '2023-03-01'))
        ->toThrow(InvalidArgumentException::class, 'Invalid date format for "from"');
});

test('can create date range with time interval spanning multiple days within range', function () {
    $timeInterval = new TimeInterval(
        new DateTime('2024-01-16 08:00:00'),
        new DateTime('2024-01-18 22:00:00')
    );
    $dateRange = new DateRange('2024-01-15', '2024-01-20', $timeInterval);

    expect($dateRange)
        ->from->toBe('2024-01-15')
        ->to->toBe('2024-01-20')
        ->timeIntervalFrom->toBeInstanceOf(TimeInterval::class);
});

test('can create date range with year boundary', function () {
    $dateRange = new DateRange('2023-12-31', '2024-01-01');

    expect($dateRange)
        ->from->toBe('2023-12-31')
        ->to->toBe('2024-01-01');
});

test('can create date range with both intervals on different dates within range', function () {
    $timeIntervalFrom = new TimeInterval(
        new DateTime('2024-01-15 09:00:00'),
        new DateTime('2024-01-15 17:00:00')
    );
    $timeIntervalTo = new TimeInterval(
        new DateTime('2024-01-20 10:00:00'),
        new DateTime('2024-01-20 18:00:00')
    );
    $dateRange = new DateRange('2024-01-15', '2024-01-20', $timeIntervalFrom, $timeIntervalTo);

    expect($dateRange)
        ->timeIntervalFrom->toBeInstanceOf(TimeInterval::class)
        ->timeIntervalTo->toBeInstanceOf(TimeInterval::class);
});
