<?php

namespace Bsmrg\LaravelNotificationChannels\Essendex;

use Bsmrg\LaravelNotificationChannels\Essendex\Exceptions\CouldNotSendNotification;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;

class EssendexChannel
{
    protected EssendexClient $client;

    private $dispatcher;

    public function __construct(EssendexClient $client, ?Dispatcher $dispatcher = null)
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
        $message = $notification->toEssendex($notifiable);

        $data = [];

        if (is_string($message)) {
            $message = EssendexMessage::create($message);
        }

        if ($to = $notifiable->routeNotificationFor('essendex', $notification)) {
            $message->setRecipient($to);
        }

        try {
            $data = $this->client->send($message);

            if ($this->dispatcher !== null) {
                $this->dispatcher->dispatch('essendex-sms', [$notifiable, $notification, $data]);
            }
        } catch (CouldNotSendNotification $e) {
            if ($this->dispatcher !== null) {
                $this->dispatcher->dispatch(
                    new NotificationFailed(
                        $notifiable,
                        $notification,
                        'essendex-sms',
                        $e->getMessage()
                    )
                );
            }
        }

        return $data;
    }
}
