<?php

declare(strict_types=1);

namespace Storm\Message;

use Storm\Contract\Message\MessageFactory;
use Storm\Contract\Serializer\MessageSerializer;
use Storm\Serializer\Payload;

use function is_array;

final readonly class GenericMessageFactory implements MessageFactory
{
    public function __construct(private MessageSerializer $messageSerializer)
    {
    }

    /**
     * @param object|array{content:array|empty,headers:array} $message
     */
    public function createMessageFrom(object|array $message): Message
    {
        // checkMe we delegate error to the serializer
        // we could normalize message here or with dependency
        // to fit requirement of payload/serializer with Header::EventType
        if (is_array($message)) {
            $message = $this->messageSerializer->deserializePayload(
                /** @phpstan-ignore-next-line  */
                new Payload($message['content'] ?? [], $message['headers'] ?? [])
            );
        }

        if ($message instanceof Message) {
            return new Message($message->event(), $message->headers());
        }

        return new Message($message);
    }
}