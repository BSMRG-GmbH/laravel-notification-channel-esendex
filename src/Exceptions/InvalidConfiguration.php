<?php

namespace Bsmrg\LaravelNotificationChannels\Esendex\Exceptions;

use Exception;

class InvalidConfiguration extends Exception
{
    /**
     * @return static
     */
    public static function configurationNotSet()
    {
        return new static('In order to send notifications via Esendex you need to add credentials in the `esendex` key of `config.services`.');
    }
}
