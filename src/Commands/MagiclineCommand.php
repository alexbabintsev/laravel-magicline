<?php

namespace alexbabintsev\Magicline\Commands;

use Illuminate\Console\Command;

class MagiclineCommand extends Command
{
    public $signature = 'laravel-magicline';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
