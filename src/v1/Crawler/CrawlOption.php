<?php
declare(strict_types=1);

namespace Acme\v1\Crawler;

use Psr\Http\Message\UriInterface;

class CrawlOption
{
// TODO 複数いけるように
    private string $includeHost;
    // TODO 複数いけるように
    private string $includePathPrefixForVisit;
    private string $includePathPrefixForSave;

    private UriInterface $startUri;

    public function __construct(
        UriInterface $startUrl,
        string $includeHost,
        string $includePathPrefix,
        string $includePathPrefixForSave
    ) {
        $this->startUri = $startUrl;
        $this->includeHost = $includeHost;
        $this->includePathPrefixForVisit = $includePathPrefix;
        $this->includePathPrefixForSave = $includePathPrefixForSave;
    }

    public function getIncludeHost(): string
    {
        return $this->includeHost;
    }

    public function getIncludePathPrefixForVisit(): string
    {
        return $this->includePathPrefixForVisit;
    }

    public function getIncludePathPrefixForSave(): string
    {
        return $this->includePathPrefixForSave;
    }


    public function getStartUri(): UriInterface
    {
        return $this->startUri;
    }
}
