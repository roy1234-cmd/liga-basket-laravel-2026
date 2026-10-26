<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('liga:info', function () {
    $this->info('Liga Basket · Laravel · almacenamiento JSON, sin MySQL');
})->purpose('Mostrar información del proyecto');
