<?php
declare(strict_types=1);

namespace Acme\v1\Writer;

class NullWriter implements IWriter
{
    public function write(string $name, string $body): void
    {
        // do nothing
    }
}
