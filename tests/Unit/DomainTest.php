<?php

declare(strict_types=1);

namespace Chronhub\Message\Tests\Unit;

use Generator;
use RuntimeException;
use Chronhub\Message\Domain;
use Chronhub\Message\Tests\UnitTestCase;
use Chronhub\Contracts\Message\Messaging;
use Chronhub\Contracts\Message\DomainEvent;
use Chronhub\Contracts\Message\DomainQuery;
use Chronhub\Message\Tests\Double\SomeEvent;
use Chronhub\Message\Tests\Double\SomeQuery;
use Chronhub\Contracts\Message\DomainCommand;
use Chronhub\Message\Tests\Double\SomeCommand;
use Chronhub\Message\Tests\Double\SomeCommandWithConstructor;

final class DomainTest extends UnitTestCase
{
    /**
     * @test
     * @dataProvider provideDomain
     */
    public function it_test_domain_content(Domain $domain): void
    {
        $this->assertEmpty($domain->headers());

        $this->assertEquals(['name' => 'steph bug'], $domain->toContent());

        if ($domain instanceof DomainCommand) {
            $this->assertEquals('command', $domain->type());
        }

        if ($domain instanceof DomainEvent) {
            $this->assertEquals('event', $domain->type());
        }

        if ($domain instanceof DomainQuery) {
            $this->assertEquals('query', $domain->type());
        }
    }

    /**
     * @test
     */
    public function it_raise_exception_when_domain_type_is_unknown(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to determine type of domain');

        $domain = new class extends Domain
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
        };

        $domain->type();
    }

    /**
     * @test
     * @dataProvider provideDomain
     */
    public function it_add_header_and_return_new_instance_of_domain(Domain $domain): void
    {
        $this->assertEmpty($domain->headers());

        $this->assertTrue($domain->hasNot('some'));
        $this->assertNull($domain->header('unknown'));

        $cloned = $domain->withHeader('name', 'steph bug');

        $this->assertNotSame($domain, $cloned);

        $this->assertTrue($cloned->has('name'));
        $this->assertNull($cloned->header('unknown header'));
        $this->assertEquals('steph bug', $cloned->header('name'));
        $this->assertEquals(['name' => 'steph bug'], $cloned->headers());
        $this->assertEquals(['name' => 'steph bug'], $cloned->toContent());
    }

    /**
     * @test
     * @dataProvider provideDomain
     */
    public function it_add_headers_and_return_new_instance_of_domain(Domain $domain): void
    {
        $this->assertEmpty($domain->headers());

        $this->assertTrue($domain->hasNot('name'));
        $this->assertNull($domain->header('unknown header'));

        $cloned = $domain->withHeaders(['name' => 'steph bug']);

        $this->assertNotEquals($domain, $cloned);

        $this->assertTrue($cloned->has('name'));
        $this->assertNull($cloned->header('unknown'));
        $this->assertEquals('steph bug', $cloned->header('name'));
        $this->assertEquals(['name' => 'steph bug'], $cloned->headers());
        $this->assertEquals(['name' => 'steph bug'], $cloned->toContent());
    }

    public function provideDomain(): Generator
    {
        $content = ['name' => 'steph bug'];

        yield [SomeCommand::fromContent($content)];
        yield [SomeEvent::fromContent($content)];
        yield [SomeQuery::fromContent($content)];

        yield [new SomeCommandWithConstructor($content)];
    }
}
