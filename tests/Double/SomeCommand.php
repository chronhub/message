<?php

declare(strict_types=1);

namespace Chronhub\Message\Tests\Double;

use Chronhub\Message\Domain;
use Chronhub\Contracts\Message\Messaging;
use Chronhub\Contracts\Message\DomainCommand;

final class SomeCommand extends Domain implements DomainCommand
{
    private array $content;

    public static function fromContent(array $content): Messaging
    {
        $self = new self();

        $self->content = $content;

        return $self;
    }

    public function toContent(): array
    {
        return $this->content;
    }
}
