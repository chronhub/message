<?php

declare(strict_types=1);

namespace Chronhub\Message\Tests\Unit;

use Chronhub\Message\Tests\UnitTestCase;
use Chronhub\Message\Tests\Stub\ObjectWithConstructableContent;

final class HasConstructableContentTest extends UnitTestCase
{
    /**
     * @test
     */
    public function it_construct_with_content(): void
    {
        $someContent = new ObjectWithConstructableContent(['name' => 'steph bug']);

        $this->assertEquals(['name' => 'steph bug'], $someContent->toContent());
        $this->assertEquals(['name' => 'steph bug'], $someContent->content);
    }

    /**
     * @test
     */
    public function it_instantiate_with_content(): void
    {
        $someContent = ObjectWithConstructableContent::fromContent(['name' => 'steph bug']);

        $this->assertEquals(['name' => 'steph bug'], $someContent->toContent());
        $this->assertEquals(['name' => 'steph bug'], $someContent->content);
    }
}
