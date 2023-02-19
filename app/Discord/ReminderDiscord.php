<?php

namespace App\Discord;

use App\Models\Reminder;
use App\Services\ReminderService;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\Embed\Embed;
use Discord\WebSockets\Event;
use Illuminate\Support\Str;

class ReminderDiscord
{
    public static function init($app, $discord)
    {
        $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord) { // 1
            if (str_starts_with($message->content, 'remind me to')) {
                self::reminderCommand($message);
            }

            if ($message->content === 'reminders') {
                $reminders = Reminder::where('user_id', $message->author->id)->get();

                $embed = new Embed($discord);
                $embed->setTitle("Your reminders:");
                $embed->type = Embed::TYPE_RICH;

                if (!$reminders || $reminders->isEmpty()) {
                    $message->reply("You don't have any reminders.");
                    return;
                }

                foreach ($reminders as $reminder) {
                    $embed->addFieldValues(
                        $reminder->message,
                        ($reminder->date ? "<t:" . $reminder->date->unix() . ":R>" : "No date set"),
                        false
                    );
                }

                $message->reply(MessageBuilder::new()->addEmbed($embed));
            }
        });
    }

    private static function reminderCommand($message)
    {
        $reminder = ReminderService::parseCommand($message->content);

        if (is_array($reminder) && isset($reminder['message'])) {
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

    public static function check($app, $discord)
    {
        $reminders = Reminder::where('date', '<=', now())->get();

        foreach ($reminders as $reminder) {
            $channel = $discord->getChannel($reminder->channel_id);

            if (!$channel) {
                $reminder->delete();
                return false;
            }

            $channel->messages->fetch($reminder->message_id)
                ->done(function ($message) use ($reminder) {
                    $message->reply('🐸');
                });

            $reminder->delete();
        }
    }
}
