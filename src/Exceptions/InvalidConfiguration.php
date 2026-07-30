<?php

namespace Bsmrg\LaravelNotificationChannels\Esendex\Exceptions;

use Exception;

class InvalidConfiguration extends Exception
{
    public static function configurationNotSet(): static
    {
        return new static('In order to send notifications via Esendex you need to add credentials in the `esendex` key of `config.services`.');
    }

    public static function entryMissing(string $entry): static
    {
        return new static($entry.' is missing from the configuration and required.');
    }
}
