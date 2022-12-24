<?php

namespace Tests\Unit;

use Illuminate\Support\Carbon;
use Tests\TestCase;
use App\Services\ReminderService;

class ReminderServiceTest extends TestCase
{
    // test parsing a new reminder without a date
    public function testParseReminderWithoutADate()
    {
        $reminder = ReminderService::parseCommand('remind me to test');

        // the reminder should be set for with a null date
        $this->assertEquals('test', $reminder['message']);
        $this->assertNull($reminder['date']);
    }

    // test parsing a new reminder for tomorrow, without "on", "at", or "in"
    public function testParseReminderForTomorrowDirectly()
    {
        $reminder = ReminderService::parseCommand('remind me to test tomorrow');

        // the reminder should be set for now + 1 day
        $this->assertEquals('test', $reminder['message']);
        $this->assertEquals(new Carbon('tomorrow'), $reminder['date']);
    }

    // test passing an ISO8601 date string to a Reminder
    public function testParseReminderWithIso8601Date()
    {
        $dateString = '2018-01-01';

        $reminder = ReminderService::parseCommand('remind me to test on ' . $dateString);

        // the reminder should be set for now + 1 day
        $this->assertEquals('test', $reminder['message']);
        $this->assertEquals(new Carbon($dateString), $reminder['date']);
    }

    // test parsing a new reminder for a relative date
    public function testParseReminderForRelativeDate()
    {
        $dateString = 'next monday';

        $reminder = ReminderService::parseCommand('remind me to test ' . $dateString);

        // the reminder should be set for next monday
        $this->assertEquals('test', $reminder['message']);
        $this->assertEquals(new Carbon($dateString), $reminder['date']);
    }

    // test parsing a new reminder for an absolute date
    public function testParseReminderForAbsoluteDate()
    {
        $dateString = 'January 6th 2008';

        $reminder = ReminderService::parseCommand('remind me to test on ' . $dateString);

        // the reminder should be set for January 6th 2008
        $this->assertEquals('test', $reminder['message']);
        $this->assertEquals(new Carbon($dateString), $reminder['date']);
    }
}
