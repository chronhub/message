<?php

declare(strict_types=1);

namespace Chronhub\Message\Tests\Unit;

use Chronhub\Message\Message;
use Chronhub\Testing\UnitTestCase;
use Chronhub\Contracts\Message\Envelop;
use Chronhub\Contracts\Message\Decorator;
use Chronhub\Message\ChainMessageDecorator;
use Chronhub\Testing\Double\Message\SomeCommand;

final class ChainMessageDecoratorTest extends UnitTestCase
{
    /**
     * @test
     */
    public function it_decorate_message(): void
    {
        $decorator = new class implements Decorator
        {
            public function decorate(Envelop $message): Envelop
            {
                return  $message->withHeader('some', 'header');
            }
        };

        $message = new Message(SomeCommand::fromContent(['foo' => 'bar']));

        $this->assertEmpty($message->headers());

        $chain = new ChainMessageDecorator($decorator);

        $decoratedMessage = $chain->decorate($message);

        $this->assertNotSame($message, $decoratedMessage);
        $this->assertEquals(['some' => 'header'], $decoratedMessage->headers());
    }

    /**
     * @test
     */
    public function it_decorate_message_with_many_decorators(): void
    {
        $decorator1 = new class implements Decorator
        {
            public function decorate(Envelop $message): Envelop
            {
                return  $message->withHeader('some', 'header');
            }
        };

        $decorator2 = new class implements Decorator
        {
            public function decorate(Envelop $message): Envelop
            {
                return  $message->withHeader('another', 'header');
            }
        };

        $message = new Message(SomeCommand::fromContent(['foo' => 'bar']));

        $this->assertEmpty($message->headers());

        $chain = new ChainMessageDecorator($decorator1, $decorator2);

        $decoratedMessage = $chain->decorate($message);

        $this->assertNotSame($message, $decoratedMessage);
        $this->assertEquals(['some' => 'header', 'another' => 'header'], $decoratedMessage->headers());
    }
}
