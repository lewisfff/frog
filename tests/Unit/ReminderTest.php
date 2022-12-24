<?php

namespace Tests\Unit;

use App\Models\Reminder;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ReminderTest extends TestCase
{
    // test creating a new reminder without arguments
    public function testCreateReminderWithoutArguments()
    {
        $reminder = new Reminder();

        $this->assertInstanceOf(Reminder::class, $reminder);
    }

    // test creating a new reminder for tomorrow
    public function testCreateReminderForTomorrow()
    {
        $dateString = 'tomorrow';

        $reminder = Reminder::factory()->create([
            'date' => $dateString,
        ]);

        // the reminder should be set for now + 1 day
        $this->assertEquals(new Carbon($dateString), $reminder->date);
    }

    // test passing an ISO8601 date string to a Reminder
    public function testCreateReminderWithIso8601Date()
    {
        $dateString = '2018-01-01';

        $reminder = Reminder::factory()->create([
            'date' => $dateString,
        ]);

        // the reminder should be set for 2018-01-01
        // $reminder->date returns a Carbon instance
        $this->assertEquals(new Carbon($dateString), $reminder->date);
    }

    // test creating a new reminder for a relative date
    public function testCreateReminderForRelativeDate()
    {
        $dateString = 'next monday';

        $reminder = Reminder::factory()->create([
            'date' => $dateString,
        ]);

        // the reminder should be set for next monday
        $this->assertEquals(new Carbon($dateString), $reminder->date);
    }

    // test creating a new reminder for an absolute date
    public function testCreateReminderForAbsoluteDate()
    {
        $dateString = 'January 6th 2008';

        $reminder = Reminder::factory()->create([
            'date' => $dateString,
        ]);

        // the reminder should be set for 2018-01-01
        $this->assertEquals(new Carbon($dateString), $reminder->date);
    }

    // test creating a new reminder without a date (this is a todo)
    public function testCreateReminderWithoutADate()
    {
        $reminder = Reminder::factory()->create([
            'date' => null,
        ]);

        // the reminder should be set for now
        $this->assertEquals(null, $reminder->date);
    }
}
