<?php

use Flux\Flux;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Stringable;

if (! function_exists('icon')) {
    function icon(string $name, array $options = []): Stringable
    {
        $variant = $options['variant'] ?? 'outline';

        return Flux::icon($name, $variant);
    }
}