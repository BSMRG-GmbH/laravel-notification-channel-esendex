<?php

namespace Bsmrg\LaravelNotificationChannels\Esendex\Exceptions;

use Exception;

class CouldNotSendNotification extends Exception
{
    /**
     * @param  Exception  $exception
     * @return static
     */
    public static function serviceRespondedWithAnError(Exception $exception)
    {
        return new static("Esendex API responded with an error '{$exception->getCode()}: {$exception->getMessage()}'");
    }
}
