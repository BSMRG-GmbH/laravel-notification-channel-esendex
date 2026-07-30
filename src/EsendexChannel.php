<?php

namespace Bsmrg\LaravelNotificationChannels\Esendex;

use Bsmrg\LaravelNotificationChannels\Esendex\Exceptions\CouldNotSendNotification;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;

class EsendexChannel
{
    protected EsendexClientInterface $client;

    private $dispatcher;

    public function __construct(EsendexClientInterface $client, ?Dispatcher $dispatcher = null)
    {
        $this->client = $client;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return object with response body data if succesful response from API | empty array if not
     *
     * @throws CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toEsendex($notifiable);

        $data = [];

        if (is_string($message)) {
            if($this->client instanceof EsendexClientV2) {
                $message = EsendexMessageV2::create($message);
            } else {
                $message = EsendexMessage::create($message);
            }
        }

        if ($to = $notifiable->routeNotificationFor('esendex', $notification)) {
            $message->setRecipient($to);
        }

        try {
            $data = $this->client->send($message);

            if ($this->dispatcher !== null) {
                $this->dispatcher->dispatch('esendex-sms', [$notifiable, $notification, $data]);
            }
        } catch (CouldNotSendNotification $e) {
            if ($this->dispatcher !== null) {
                $this->dispatcher->dispatch(
                    new NotificationFailed(
                        $notifiable,
                        $notification,
                        'esendex-sms',
                        $e->getMessage()
                    )
                );
            }
        }

        return $data;
    }
}
