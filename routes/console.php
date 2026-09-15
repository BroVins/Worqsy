<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('worqsy:about', function () {
    $this->info('Worqsy V1 - Project & Work Management Platform');
})->purpose('Display Worqsy application information');
