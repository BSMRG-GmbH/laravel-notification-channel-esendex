<?php

namespace Bsmrg\LaravelNotificationChannels\Esendex;

class EsendexMessage
{
    public string $body;

    public $recipient;

    public static function create($body = '')
    {
        return new static($body);
    }

    public function __construct($body = '')
    {
        if (! empty($body)) {
            $this->body = trim($body);
        }
    }

    public function setBody($body)
    {
        $this->body = trim($body);

        return $this;
    }

    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;

        return $this;
    }
}
