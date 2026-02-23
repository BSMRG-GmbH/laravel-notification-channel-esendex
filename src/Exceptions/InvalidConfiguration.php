<?php

namespace Bsmrg\LaravelNotificationChannels\Essendex\Exceptions;

use Exception;

class InvalidConfiguration extends Exception
{
    /**
     * @return static
     */
    public static function configurationNotSet()
    {
        return new static('In order to send notifications via Essendex you need to add credentials in the `essendex` key of `config.services`.');
    }
}
