<?php

declare(strict_types=1);

namespace Chronhub\Message;

use RuntimeException;
use InvalidArgumentException;
use Chronhub\Contracts\Message\Envelop;
use Chronhub\Contracts\Message\Messaging;
use function count;

final class Message implements Envelop
{
    use HasHeaders;

    private object $event;

    public function __construct(object $event, array $headers = [])
    {
        $this->setUp($event, $headers);
    }

    public function event(): object
    {
        if ($this->event instanceof Messaging) {
            return clone $this->event->withHeaders($this->headers);
        }

        return clone $this->event;
    }

    public function withHeader(string $key, mixed $value): Message
    {
        $message = clone $this;

        $message->headers[$key] = $value;

        return $message;
    }

    public function withHeaders(array $headers): Message
    {
        $message = clone $this;

        $message->headers = $headers;

        return $message;
    }

    public function isMessaging(): bool
    {
        return $this->event instanceof Messaging;
    }

    /**
     * Check headers consistency between
     * constructor headers and event headers
     */
    private function setUp(object $event, array $headers): void
    {
        if ($event instanceof self) {
            throw new InvalidArgumentException('Message event can not be an instance of itself');
        }

        if (! $event instanceof Messaging) {
            $this->event = $event;
            $this->headers = $headers;

            return;
        }

        $expectedHeaders = match (true) {
            count($event->headers()) === 0, $event->headers() === $headers => $headers,
            count($headers) === 0 => $event->headers(),

            default => throw new RuntimeException('Invalid headers consistency for event class '.$event::class)
        };

        $this->event = $event->withHeaders([]);
        $this->headers = $expectedHeaders;
    }
}
