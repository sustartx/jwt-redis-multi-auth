<?php

namespace SuStartX\JWTRedisMultiAuth\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class LoggedInEvent
{
    use Dispatchable, SerializesModels;

    public $post;

    public function __construct()
    {

    }
}
