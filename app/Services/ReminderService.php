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
        $initial = $command;

        $command = strtolower($command);
        $command = str_replace('remind me to ', '', $command);

        $message = '';
        $date = null;

        // array of words that can be used to indicate a date
        $dateIndicators = [' on ', ' at ', ' in ', ' next ', ' this '];
        $dateString = null;

        // if any of the $dateIndicators are found, find the last occurrence of one of them and then split the command into two parts, the message and the date
        // explode cannot be used as it will split the command into multiple parts if multiple $dateIndicators are found
        // also strip out the $dateIndicator string from the date part
        // if no $dateIndicators are found, the message is the entire command and the date is null
        foreach ($dateIndicators as $dateIndicator) {
            $pos = strrpos($command, $dateIndicator);
            if ($pos !== false) {
                $message = substr($command, 0, $pos);
                $dateString = trim(substr($command, $pos + strlen($dateIndicator)));
                break;
            }
        }

        // if no $dateIndicator was found, assume the last word is the date for example "remind me to test tomorrow"
        if ($dateString === null) {
            $message = substr($command, 0, strrpos($command, ' '));
            $dateString = trim(substr($command, strrpos($command, ' ')));
        }

        // can a Carbon date be made from $dateString
        // put this in a try/catch block
        try {
            $date = \Carbon\Carbon::parse($dateString);
        } catch (\Exception $e) {
            // if it fails then they probably didn't put a date in the command
            // just return the initial message and null date
            $message = $command;
            $date = null;
        }

        return compact('message', 'date');
    }
}
