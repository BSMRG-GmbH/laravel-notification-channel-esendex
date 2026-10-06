<?php

namespace Bsmrg\LaravelNotificationChannels\Esendex;

use Bsmrg\LaravelNotificationChannels\Esendex\Exceptions\CouldNotSendNotification;
use Bsmrg\LaravelNotificationChannels\Esendex\Exceptions\NoRecipientProvided;
use GuzzleHttp\Client;

interface EsendexClientInterface
{
    public function __construct(Client $client, array $config);

    /**
     * Send the Message.
     *
     * @throws NoRecipientProvided
     * @throws CouldNotSendNotification
     */
    public function send(EsendexMessage $message);
    
}
