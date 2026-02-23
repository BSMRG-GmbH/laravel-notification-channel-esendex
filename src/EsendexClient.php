<?php

namespace Bsmrg\LaravelNotificationChannels\Esendex;

use Bsmrg\LaravelNotificationChannels\Esendex\Exceptions\CouldNotSendNotification;
use Bsmrg\LaravelNotificationChannels\Esendex\Exceptions\NoRecipientProvided;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

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
        // TODO: use cache and session like as per https://developers.esendex.com/api-reference/#authentication
        return base64_encode($this->user.':'.$this->apiKey);
    }

    /**
     * Send the Message.
     *
     * @throws CouldNotSendNotification
     */
    public function send(EsendexMessage $message)
    {
        if (empty($message->originator)) {
            $message->setOriginator(config('services.essendex.originator'));
        }
        if (empty($message->recipient)) {
            throw new NoRecipientProvided;
        }

        try {
            $response = $this->client->request('POST', 'https://api.esendex.com/v1.0/messagedispatcher', [
                'json' => [
                    'accountreference' => $this->account,
                    'messages' => [
                        [
                            'to' => trim($message->recipient, ' +.-()'),
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
            Log::debug('Essendex response', ['response' => $responseAsJson]);

            return $responseAsJson;
        } catch (Exception $exception) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($exception);
        }
    }
}
