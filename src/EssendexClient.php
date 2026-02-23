<?php

namespace Bsmrg\LaravelNotificationChannels\Essendex;

use Bsmrg\LaravelNotificationChannels\Essendex\Exceptions\CouldNotSendNotification;
use Bsmrg\LaravelNotificationChannels\Essendex\Exceptions\NoRecipientProvided;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class EssendexClient
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
    public function send(EssendexMessage $message)
    {
        if (empty($message->originator)) {
            $message->setOriginator(config('services.essendex.originator'));
        }
        if (empty($message->recipient)) {
            throw new NoRecipientProvided;
        }

        try {
            $response = $this->client->request('POST', 'https://api.essendex.com/v1.0/messagedispatcher', [
                'body' => [
                    'accountreference' => $this->account,
                    'messages' => [
                        'to' => trim($message->recipient, ' +.-()'),
                        'body' => $message->body,
                    ],
                ],
                'headers' => [
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
