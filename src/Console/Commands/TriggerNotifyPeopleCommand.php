<?php

namespace ME\EmCore\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use ME\EmCore\Http\Controllers\NotifyPersonController;

class TriggerNotifyPeopleCommand extends Command
{
    protected $signature = 'emcore:notify-people-trigger';

    protected $description = 'Trigger notify people workflow';

    public function handle(): int
    {
        $controller = app(NotifyPersonController::class);

        $response = app()->call([$controller, 'trigger'], [
            'request' => Request::create('/notify-people/trigger', 'GET'),
        ]);

        $data = method_exists($response, 'getData') ? $response->getData(true) : [];

        if (!is_array($data)) {
            $this->error('Unexpected response from notify trigger.');

            return self::FAILURE;
        }

        $this->info((string) ($data['message'] ?? 'Notify trigger executed.'));
        $this->line('Total people: ' . (string) ($data['total_people'] ?? 0));
        $this->line('Total notifications: ' . (string) ($data['total_notifications'] ?? 0));

        return self::SUCCESS;
    }
}
