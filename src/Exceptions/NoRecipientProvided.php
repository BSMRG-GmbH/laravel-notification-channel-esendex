<?php

namespace Bsmrg\LaravelNotificationChannels\Essendex\Exceptions;

use Exception;

class NoRecipientProvided extends Exception
{
    /**
     * @return static
     */
    public function __construct()
    {
        parent::__construct('A recipient must be provided');
    }
}
