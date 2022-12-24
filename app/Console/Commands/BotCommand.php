<?php

namespace App\Console\Commands;

use App\Discord\ReminderDiscord;
use App\Services\ChangelogService;
use Illuminate\Console\Command;
use Discord\Discord;
use React\EventLoop\Loop;

class BotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'start bot';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $discord = new Discord([
            'token' => env('DISCORD_TOKEN'),
            'loop' => Loop::get()
        ]);

        $discord->on('ready', function (Discord $discord) {
            ReminderDiscord::init($this, $discord);

            Loop::addPeriodicTimer(5, function () use ($discord) {
                ReminderDiscord::check($this, $discord);
            });
        });

        ChangelogService::send($this, $discord);

        $discord->run();
    }
}
