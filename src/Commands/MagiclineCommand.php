<?php

namespace alexbabintsev\Magicline\Commands;

use alexbabintsev\Magicline\Magicline;
use Illuminate\Console\Command;

class MagiclineCommand extends Command
{
    public $signature = 'magicline:test {--endpoint=customers : API endpoint to test}';

    public $description = 'Test Magicline API connection and endpoints';

    public function handle(Magicline $magicline): int
    {
        $endpoint = $this->option('endpoint');

        $this->info('Testing Magicline API connection...');

        try {
            $result = match ($endpoint) {
                'customers' => $magicline->customers()->list(0, 10),
                'devices' => $magicline->devices()->list(),
                'employees' => $magicline->employees()->list(0, 10),
                'memberships' => $magicline->memberships()->getOffers(),
                'studios' => $magicline->studios()->getUtilization(),
                default => $magicline->customers()->list(0, 5),
            };

            $this->info('✅ API connection successful!');
            $this->line('Response:');
            $this->line(json_encode($result, JSON_PRETTY_PRINT));

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ API connection failed:');
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
