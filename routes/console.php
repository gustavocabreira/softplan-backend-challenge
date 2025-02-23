<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('upload:handle')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('subscriber:send-email')
    ->everyMinute()
    ->withoutOverlapping();
