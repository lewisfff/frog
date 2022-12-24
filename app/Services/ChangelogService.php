<?php

namespace App\Services;

use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Embed\Embed;
use Illuminate\Support\Carbon;

class ChangelogService
{
    public static function send($app, $discord)
    {
        $discord->on('ready', function (Discord $discord) {

            $files = scandir(storage_path('app/changelog'));
            $latest = end($files);

            $content = file_get_contents(storage_path('app/changelog/' . $latest));

            // the filename has the date in it, so we can use that to get the date
            $date = Carbon::createFromFormat('Ymd', str_replace('.txt', '', $latest));

            $embed = new Embed($discord);
            $embed->setTitle('Changelog');
            $embed->setDescription($content);
            $embed->setTimestamp($date->unix());

            $msg = MessageBuilder::new()
            ->setContent("Froget has been restarted!")
            ->addEmbed($embed);

            $channel = $discord->getChannel(env('CHANGELOG_CHANNEL_ID'));
            $channel->sendMessage($msg);
        });
    }
}
