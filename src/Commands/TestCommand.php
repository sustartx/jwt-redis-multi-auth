<?php

namespace SuStartX\JWTRedisMultiAuth\Commands;

use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $hidden = false;

    protected $signature = 'jwtredismultiauth:test';

    protected $description = 'Test command.';

    public function handle()
    {
        $this->info('Test command...');
    }
}
