<?php
/**
 * This file contains custom helper functions.
 *
 * A lot of these functions are replacements for Laravel Facades, which aren't enabled by default in lumen.
 */

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;

/**
 * Allows one to run artisan commands from within the app.
 *
 * If run with no arguments, simply returns the object. If run with arguments, runs call, and returns that function.
 *
 * @param  mixed[] $args
 * @return ConsoleKernel|int
 */
function artisan(...$args) {
    $artisan = app(ConsoleKernel::class);
    if (!count($args)) return $artisan;

    return $artisan->call(...$args);
}
