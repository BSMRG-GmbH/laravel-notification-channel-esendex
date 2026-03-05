<?php

namespace Bsmrg\LaravelNotificationChannels\Esendex;

use Bsmrg\LaravelNotificationChannels\Esendex\Exceptions\CouldNotSendNotification;
use Bsmrg\LaravelNotificationChannels\Esendex\Exceptions\NoRecipientProvided;
use Exception;
use GuzzleHttp\Client;

class EsendexClient
{
    protected $client;

    protected string $account;

    protected string $apiKey;

    protected string $user;

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
        $this->user = $config['api_user'];
    }

    protected function getAuthorizationKey(): string
    {
        // TODO: use cache and session as per https://developers.esendex.com/api-reference/#authentication
        return base64_encode($this->user.':'.$this->apiKey);
    }

    /**
     * Send the Message.
     *
     * @throws CouldNotSendNotification
     */
    public function send(EsendexMessage $message)
    {
        if (empty($message->recipient)) {
            throw new NoRecipientProvided;
        }

        $recipient = $message->recipient;
        if (! app()->isProduction()) {
            $recipient = config('services.esendex.test_recipient');
            if (! $recipient) {
                throw new NoRecipientProvided('No `test_recipient` set, but app is not in production!');
            }
        }

        try {
            $response = $this->client->request('POST', 'https://api.esendex.com/v1.0/messagedispatcher', [
                'json' => [
                    'accountreference' => $this->account,
                    'messages' => [
                        [
                            'to' => trim($recipient, ' +.-()'),
                            'body' => $message->body,
                        ],
                    ],
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Basic '.$this->getAuthorizationKey(),
                ],
            ]);

            $responseAsJson = json_decode($response->getBody()->__toString());

            return $responseAsJson;
        } catch (Exception $exception) {
            throw CouldNotSendNotification::errorOccured($exception);
        }
    }
}
