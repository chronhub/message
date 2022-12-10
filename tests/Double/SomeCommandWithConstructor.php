<?php

declare(strict_types=1);

namespace Chronhub\Message\Tests\Double;

use Chronhub\Message\Domain;
use Chronhub\Contracts\Message\DomainCommand;
use Chronhub\Message\HasConstructableContent;

final class SomeCommandWithConstructor extends Domain implements DomainCommand
{
    use HasConstructableContent;
}
