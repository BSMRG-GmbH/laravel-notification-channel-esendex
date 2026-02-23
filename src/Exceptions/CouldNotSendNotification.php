<?php

namespace Bsmrg\LaravelNotificationChannels\Essendex\Exceptions;

use Exception;

class CouldNotSendNotification extends Exception
{
    /**
     * @param  Exception  $exception
     * @return static
     */
    public static function serviceRespondedWithAnError(Exception $exception)
    {
        return new static("Essendex API responded with an error '{$exception->getCode()}: {$exception->getMessage()}'");
    }
}
