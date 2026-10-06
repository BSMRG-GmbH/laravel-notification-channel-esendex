<?php

namespace Bsmrg\LaravelNotificationChannels\Esendex;

use Bsmrg\LaravelNotificationChannels\Esendex\Types\Attachment;
use Bsmrg\LaravelNotificationChannels\Esendex\Types\MessageType;
use Illuminate\Support\Carbon;

class EsendexMessage
{
    public string $text;

    /**
     * @var array<string>
     */
    public array $recipients = [];

    public ?Attachment $attachment = null;

    public ?MessageType $messageType = null;

    public ?Carbon $validity = null;

    public ?string $messageTitle = null;

    public ?array $metaData = [];

    public static function create($text = '')
    {
        return new static($text);
    }

    public function __construct(string $text = '', ?MessageType $messageType = null)
    {
        if (! empty($text)) {
            $this->text = trim($text);
        }

        $this->messageType = $messageType;
    }

    public function setBody(string $text, null|string|array|Attachment $attachment = null, ?string $attachmentFileName = null, ?string $attachmentDescription = null)
    {
        $this->text = trim($text);
        
        if($attachment) {
            $this->attachment = Attachment::from($attachment, $attachmentFileName, $attachmentDescription);
        }

        return $this;
    }

    public function attach(string|array|Attachment $attachment, ?string $fileName = null, ?string $description = null)
    {
        $this->attachment = Attachment::from($attachment, $fileName, $description);

        return $this;
    }

    public function setMessageType(MessageType $messageType)
    {
        $this->messageType = $messageType;

        return $this;
    }

    public function setRecipient(string $recipient)
    {
        $this->recipients = [$recipient];

        return $this;
    }
    
    public function addRecipient(string $recipient)
    {
        $this->recipients[] = $recipient;

        return $this;
    }

    /**
     * @param array<string> $recipients
     */
    public function setRecipients(array $recipients)
    {
        $this->recipients = $recipients;

        return $this;
    }

    public function setValidity(mixed $validity)
    {
        $this->validity = Carbon::parse($validity);

        return $this;
    }

    public function setMetaData(array $metaData) {
        $this->metaData = $metaData;

        return $this;
    }

    public function setMessageTitle(string $title) {
        $this->messageTitle = $title;

        return $this;
    }
}
