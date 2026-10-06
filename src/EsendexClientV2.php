<?php

namespace Bsmrg\LaravelNotificationChannels\Esendex;

use Bsmrg\LaravelNotificationChannels\Esendex\Exceptions\CouldNotSendNotification;
use Bsmrg\LaravelNotificationChannels\Esendex\Exceptions\NoRecipientProvided;
use Exception;
use GuzzleHttp\Client;

class EsendexClientV2 implements EsendexClientInterface
{
    protected $client;

    protected string $account;

    protected string $apiKey;

    protected ?string $originator;

    /**
     * MessagebirdClient constructor.
     *
     * @param  Client  $client
     * @param $config API Key and user for Essendex API
     */
    public function __construct(Client $client, array $config)
    {
        $this->client = $client;
        $this->account = $config['account'];
        $this->apiKey = $config['api_key'];
        $this->originator = $config['originator'] ?? null;
    }

    /**
     * Send the Message.
     * 
     * @throws NoRecipientProvided
     * @throws CouldNotSendNotification
     */
    public function send(EsendexMessage $message)
    {
       if (empty($message->recipients) || empty($message->recipients[0])) {
            throw new NoRecipientProvided;
        }

        $recipients = $message->recipients;
        if (! app()->isProduction()) {
            $dummyRecipient = config('services.esendex.test_recipient');
            if (! $dummyRecipient) {
                throw new NoRecipientProvided('No `test_recipient` set, but app is not in production!');
            }
            $recipients = [$dummyRecipient];
        }

        $recipientData = [];
        foreach($recipients as $recipient) {
            $recipientData[] = [
                'msisdn' => $recipient,
            ];
        }

        $requestData = [
            'accountReference' => $this->account,
            'channel' => 'SMS',
            'characterSet' => 'auto',
        ];

        if($this->originator) {
            $requestData['from'] = $this->originator;
        }

        if($message->validity) {
            $requestData['validity'] = $message->validity->toIso8601String;
        }

        if($message->messageType) {
            $requestData['messageType'] = $message->messageType->value;
        }

        if($message->messageTitle) {
            $requestData['name'] = $message->messageTitle;
        }

        $requestData['body'] = [
            'text' => $message->text
        ];

        if($message->attachment) {
            $requestData['body']['attachment'] = [
               'attachmentUrl' =>  $message->attachment->attachmentUrl,
               'fileName' => $message->attachment->fileName,
               'description' => $message->attachment->description
            ];
        }

        $requestData['recipients'] = $recipientData;

        try {
            $response = $this->client->request('POST', 'https://api.esendex.de/v2/messages', [
                'json' => $requestData,
                'headers' => [
                    'Accept' => 'application/json',
                    'X-Api-Key' => $this->apiKey,
                ],
            ]);

            $responseAsJson = json_decode($response->getBody()->__toString());

            return $responseAsJson;
        } catch (Exception $exception) {
            throw CouldNotSendNotification::errorOccured($exception);
        }
    }
}
