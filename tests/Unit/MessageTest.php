<?php

declare(strict_types=1);

namespace Chronhub\Message\Tests\Unit;

use stdClass;
use Generator;
use RuntimeException;
use Chronhub\Message\Domain;
use Chronhub\Message\Message;
use InvalidArgumentException;
use Chronhub\Message\Tests\UnitTestCase;
use Chronhub\Message\Tests\Double\SomeEvent;
use Chronhub\Message\Tests\Double\SomeQuery;
use Chronhub\Message\Tests\Double\SomeCommand;
use Chronhub\Message\Tests\Double\SomeCommandWithConstructor;

final class MessageTest extends UnitTestCase
{
    /**
     * @test
     * @dataProvider provideObject
     */
    public function it_instantiate_message_with_object(object $event): void
    {
        $message = new Message($event);

        $this->assertFalse($message->isMessaging());
        $this->assertEquals($event::class, $message->event()::class);
    }

    /**
     * @test
     * @dataProvider provideDomain
     */
    public function it_instantiate_message_with_domain_instance(Domain $domain): void
    {
        $message = new Message($domain);

        $this->assertTrue($message->isMessaging());
        $this->assertEquals($domain, $message->event());
        $this->assertNotSame($domain, $message->event());

        $this->assertTrue($message->has('some'));
        $this->assertEquals(['some' => 'header'], $message->headers());
        $this->assertEquals('header', $message->header('some'));
    }

    /**
     * @test
     */
    public function it_can_add_header_to_message_with_an_event_without_header(): void
    {
        $message = new Message(SomeCommand::fromContent([]), ['some' => 'header']);

        $this->assertEquals(['some' => 'header'], $message->headers());
    }

    /**
     * @test
     */
    public function it_can_add_header_to_message_with_an_event_when_headers_matched(): void
    {
        $message = new Message(
            SomeCommand::fromContent([])->withHeaders(['some' => 'header']),
            ['some' => 'header']
        );

        $this->assertEquals(['some' => 'header'], $message->headers());
    }

    /**
     * @test
     * @dataProvider provideDomain
     */
    public function it_raise_exception_when_event_headers_differ_from_headers_on_instantiation(Domain $domain): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid headers consistency for event class '.$domain::class);

        new Message($domain, ['another' => 'header']);
    }

    /**
     * @test
     */
    public function it_raise_exception_when_event_is_an_instance_of_message(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Message event can not be an instance of itself');

        new Message(new Message(new stdClass()));
    }

    /**
     * @test
     * @dataProvider provideDomain
     */
    public function it_add_header_to_message(Domain $domain): void
    {
        $message = new Message($domain);

        $this->assertEquals(['some' => 'header'], $message->headers());

        $cloned = $message->withHeader('another', 'header');

        $this->assertNotSame($cloned, $domain);
        $this->assertEquals(['some' => 'header', 'another' => 'header'], $cloned->headers());
    }

    /**
     * @test
     * @dataProvider provideDomain
     */
    public function it_override_headers_to_message(Domain $domain): void
    {
        $message = new Message($domain);

        $this->assertEquals(['some' => 'header'], $message->headers());

        $cloned = $message->withHeaders(['another' => 'header']);

        $this->assertNotSame($cloned, $domain);
        $this->assertEquals(['another' => 'header'], $cloned->headers());
    }

    /**
     * @test
     * @dataProvider provideDomain
     */
    public function it_access_event_from_message_with_headers(Domain $domain): void
    {
        $message = new Message($domain);

        $this->assertEquals($domain, $message->event());
        $this->assertNotSame($domain, $message->event());
    }

    public function provideDomain(): Generator
    {
        $headers = ['some' => 'header'];
        $content = ['name' => 'steph bug'];

        yield [SomeCommand::fromContent($content)->withHeaders($headers)];
        yield [SomeEvent::fromContent($content)->withHeaders($headers)];
        yield [SomeQuery::fromContent($content)->withHeaders($headers)];

        yield [(new SomeCommandWithConstructor($content))->withHeaders($headers)];
    }

    public function provideObject(): Generator
    {
        yield [new stdClass()];
        yield [new class()
        {
        }, ];
        yield [(fn (): bool => true)(...)];
    }
}
