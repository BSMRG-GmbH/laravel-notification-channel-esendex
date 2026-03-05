<?php

namespace Bsmrg\LaravelNotificationChannels\Esendex\Exceptions;

use Exception;

class CouldNotSendNotification extends Exception
{
    /**
     * @param  Exception  $exception
     * @return static
     */
    public static function errorOccured(Exception $exception)
    {
        return new static("There was an error while trying to send a notification: Code '{$exception->getCode()}: {$exception->getMessage()}'");
    }
}
