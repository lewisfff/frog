<?php

namespace App\Discord;

use App\Models\Reminder;
use App\Services\ReminderService;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;

class ReminderDiscord
{
    public static function init($app, $discord)
    {
        $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) { // 1
            if (str_starts_with($message->content, 'remind me to')) {

                $reminder = ReminderService::parseCommand($message->content);

                if (is_array($reminder) && isset($reminder['date']) && isset($reminder['message'])) {
                    $reminder = Reminder::create([
                        'date' => $reminder['date'],
                        'message' => $reminder['message'],
                        'user_id' => $message->author->id,
                        'channel_id' => $message->channel_id,
                        'message_id' => $message->id,
                    ]);
                }

                if ($reminder instanceof Reminder) {
                    $message->react('🐸');
                }
            }
        });
    }

    public static function check($app, $discord)
    {
        $reminders = Reminder::where('date', '<=', now())->get();

        foreach ($reminders as $reminder) {
            $channel = $discord->getChannel($reminder->channel_id);

            $channel->messages->fetch($reminder->message_id)
                ->done(function ($message) use ($reminder) {
                    $message->reply('🐸');
                });

            $reminder->delete();
        }
    }
}
