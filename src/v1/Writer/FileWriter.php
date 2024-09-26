<?php
declare(strict_types=1);

namespace Acme\v1\Writer;

class FileWriter implements IWriter
{
    private string $outputDir;

    public function __construct(string $outputDir)
    {
        if (!is_dir($outputDir)) {
            throw new \InvalidArgumentException('Output directory does not exist.');
        }
        if (!is_writable($outputDir)) {
            throw new \InvalidArgumentException('Output directory is not writable.');
        }

        $this->outputDir = $outputDir;
    }

    public function write(string $name, string $body): void
    {
        file_put_contents($this->outputDir . '/' . $name, $body);
    }
}
