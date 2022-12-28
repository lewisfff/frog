<?php

namespace App\Services;

class ReminderService
{
    /**
     * Parse a text command and return the reminder string and an absolute dateTime.
     * The text command should be in the format:
     * "remind me to <message> on <date>"
     * or
     * "remind me to <message> at <time> on <date>"
     * or
     * "remind me to <message> in <duration>"
     */
    public static function parseCommand(string $command): array
    {
        $command = strtolower($command);
        $command = str_replace('remind me to ', '', $command);

        $message = '';
        $date = null;
        $dateIndicators = [' on ', ' at ', ' in ', ' next ', ' this '];
        $usedIndicator = null;
        $dateString = null;
        $pos = false;

        // find the last occurrence of a $dateIndicator
        foreach ($dateIndicators as $dateIndicator) {
            // if pos is greater than the current pos, set it as the new pos
            if (strrpos($command, $dateIndicator) > $pos) {
                $pos = strrpos($command, $dateIndicator);
                $usedIndicator = $dateIndicator;
            }
        }

        if ($pos !== false) {
            $message = substr($command, 0, $pos);
            $dateString = trim(substr($command, $pos + strlen($usedIndicator)));
        }

        // if no $usedIndicator was found, assume the last word is the date for example "remind me to test tomorrow"
        if ($dateString === null) {
            $lastSpacePos = strrpos($command, ' ');
            $message = substr($command, 0, $lastSpacePos);
            $dateString = trim(substr($command, $lastSpacePos));
        }

        // try to create a Carbon date from $dateString
        try {
            $date = \Carbon\Carbon::parse($dateString);

            // if the date is in the past, add a day to it
            if ($date->isPast()) {
                $date->addDay();
            }
        } catch (\Exception $e) {
            // if it fails then they probably didn't put a date in the command
            // just return the initial message and null date
            $message = $command;
            $date = null;
        }

        return compact('message', 'date');
    }
}
