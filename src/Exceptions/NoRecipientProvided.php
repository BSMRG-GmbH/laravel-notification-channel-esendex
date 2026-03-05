<?php

namespace Bsmrg\LaravelNotificationChannels\Esendex\Exceptions;

use Exception;

class NoRecipientProvided extends Exception
{
    /**
     * @return static
     */
    public function __construct($message = '')
    {
        parent::__construct($message ?? 'A recipient must be provided');
    }
}
