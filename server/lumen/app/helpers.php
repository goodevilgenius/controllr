<?php
/**
 * This file contains custom helper functions.
 *
 * A lot of these functions are replacements for Laravel Facades, which aren't enabled by default in lumen.
 */

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Psr\Log\LoggerInterface;

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

/**
 * Returns an instance of LoggerInterface. Useful for debugging.
 *
 * @param  string $tolog A string to log.
 * @return LoggerInterface
 */
function logger(string $tolog = null): LoggerInterface {
    $logger = app(LoggerInterface::class);

    if (!empty($tolog)) $logger->info($tolog);

    return $logger;
}
