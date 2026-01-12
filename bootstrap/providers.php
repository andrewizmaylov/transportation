<?php

declare(strict_types=1);

use Src\ServiceProvider;

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
    ServiceProvider::class,
];
