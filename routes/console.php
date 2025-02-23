<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('upload:handle')
    ->everyMinute()
    ->withoutOverlapping();
