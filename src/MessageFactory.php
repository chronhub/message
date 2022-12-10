<?php

declare(strict_types=1);

namespace Chronhub\Message;

use Chronhub\Contracts\Message\Envelop;
use Chronhub\Contracts\Message\Factory;
use Chronhub\Contracts\Support\Serializer\MessageSerializer;
use function is_array;

final class MessageFactory implements Factory
{
    public function __construct(private readonly MessageSerializer $serializer)
    {
    }

    public function __invoke(object|array $message): Envelop
    {
        if (is_array($message)) {
            $message = $this->serializer->unserializeContent($message)->current();
        }

        if ($message instanceof Message) {
            return new Message($message->event(), $message->headers());
        }

        return new Message($message);
    }
}
