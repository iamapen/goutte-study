<?php
declare(strict_types=1);

namespace Acme\v1\Writer;

interface IWriter
{
    public function write(string $name, string $body): void;
}
