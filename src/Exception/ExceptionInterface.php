<?php

declare(strict_types=1);

namespace Ladina\CFONB\Exception;

/**
 * Marker interface implemented by every exception thrown by this library.
 *
 * It lets consumers catch any library error with a single catch block:
 *
 *     catch (\Ladina\CFONB\Exception\ExceptionInterface $e) { ... }
 */
interface ExceptionInterface extends \Throwable
{
}
