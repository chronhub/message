<?php

declare(strict_types=1);

namespace Chronhub\Message;

use Chronhub\Contracts\Message\Envelop;
use Chronhub\Contracts\Message\Decorator;

final class ChainMessageDecorator implements Decorator
{
    private array $messageDecorators;

    public function __construct(Decorator ...$messageDecorators)
    {
        $this->messageDecorators = $messageDecorators;
    }

    public function decorate(Envelop $message): Envelop
    {
        foreach ($this->messageDecorators as $messageDecorator) {
            $message = $messageDecorator->decorate($message);
        }

        return $message;
    }
}
