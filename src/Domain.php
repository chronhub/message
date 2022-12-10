<?php

declare(strict_types=1);

namespace Chronhub\Message;

use RuntimeException;
use Chronhub\Contracts\Message\Messaging;
use Chronhub\Contracts\Message\DomainEvent;
use Chronhub\Contracts\Message\DomainQuery;
use Chronhub\Contracts\Message\DomainCommand;

abstract class Domain implements Messaging
{
    use HasHeaders;

    public function withHeader(string $header, null|int|float|string|bool|array|object $value): self
    {
        $domain = clone $this;

        $domain->headers[$header] = $value;

        return $domain;
    }

    public function withHeaders(array $headers): self
    {
        $domain = clone $this;

        $domain->headers = $headers;

        return $domain;
    }

    public function type(): string
    {
        return match (true) {
            $this instanceof DomainCommand => DomainCommand::TYPE,
            $this instanceof DomainEvent => DomainEvent::TYPE,
            $this instanceof DomainQuery => DomainQuery::TYPE,
            default => throw new RuntimeException('Unable to determine type of domain')
        };
    }
}
