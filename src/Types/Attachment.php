<?php

namespace Bsmrg\LaravelNotificationChannels\Esendex\Types;

class Attachment
{
    public function __construct(public string $attachmentUrl, public string $fileName, public string $description)
    {
    }

    public static function from(array|self|string $attachment, ?string $fileName = null, ?string $description = null): static
    {
        if($attachment instanceof self) {
            return $attachment;
        }

        if(is_array($attachment)) {
            return new self($attachment['attachmentUrl'], $attachment['fileName'], $attachment['description']);
        }

        return new self($attachment, $fileName, $description);
    }
}