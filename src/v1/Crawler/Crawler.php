<?php
declare(strict_types=1);

namespace Acme\v1\Crawler;

use Acme\v1\Writer\IWriter;
use Nyholm\Psr7\Uri;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\BrowserKit\HttpBrowser;

class Crawler implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private HttpBrowser $browser;
    private IWriter $writer;

    private array $visited = [];
    private int $savedCount = 0;

    public function __construct(HttpBrowser $browser, IWriter $writer)
    {
        $this->browser = $browser;
        $this->writer = $writer;
        $this->setLogger(new NullLogger());
    }

    public function crawlLinks(CrawlOption $option): void
    {
        $startUri = $option->getStartUri();
        $strNormalizedStartUri = (string)$startUri->withFragment('');
        $this->logger->info("crawlLinks: $strNormalizedStartUri");
        if (isset($this->visited[$strNormalizedStartUri])) {
            $this->logger->debug("Already visited: $strNormalizedStartUri\n", ['crawl' => $strNormalizedStartUri]);
            return;
        }

        $host = $option->getIncludeHost();
        $pathPrefixForVisit = $option->getIncludePathPrefixForVisit();
        $pathPrefixForSave = $option->getIncludePathPrefixForSave();

        if (isset($this->visited[$strNormalizedStartUri])) {
            $this->logger->debug(
                '訪問済み',
                ['crawl' => $strNormalizedStartUri]
            );
            return;
        }

        if ($startUri->getHost() !== $host || !str_contains($startUri->getPath(), $pathPrefixForVisit)) {
            $this->logger->debug(
                'クロール対象のhost/pathじゃない',
                [
                    'crawl' => $strNormalizedStartUri,
                ]
            );
            return;
        }

        // クロール実行
        $crawler = $this->browser->request('GET', $strNormalizedStartUri);
        $this->visited[$strNormalizedStartUri] = true;

        // 保存対象なら保存
        if (str_contains($startUri->getPath(), $pathPrefixForSave)) {
            $htmlContent = $crawler->html();
            $fileName = urlencode($strNormalizedStartUri) . '.html';
            $this->writer->write($fileName, $htmlContent);
            $this->logger->info(
                sprintf('saved: %s', $strNormalizedStartUri),
                [
                    'crawl' => $strNormalizedStartUri,
                    'fileName' => $fileName,
                ]
            );
            $this->savedCount++;
        }

        // リンク収集
        $links = $crawler->filter('a')->links();
        $this->logger->info(
            sprintf("ページ内にリンク %s 件あり\n", number_format(count($links))),
            ['crawl' => $strNormalizedStartUri]
        );

        foreach ($links as $crawlerLink) {
            $linkUri = new Uri($crawlerLink->getUri());
            $strNormalizedLinkUri = (string)$linkUri->withFragment('');
            if (isset($this->visited[$strNormalizedLinkUri])) {
                $this->logger->debug(
                    '既に訪問済み',
                    [
                        'crawl' => $strNormalizedStartUri,
                        'linkUrl' => $strNormalizedLinkUri,
                    ]
                );
                continue;
            }
            if ($linkUri->getHost() !== $host || !str_contains($linkUri->getPath(), $pathPrefixForVisit)) {
                $this->logger->debug(
                    'クロール対象のhost,pathじゃない',
                    [
                        'crawl' => $strNormalizedStartUri,
                        'linkUrl' => $strNormalizedLinkUri,
                    ]
                );
                continue;
            }

            $nextOption =
                new CrawlOption(
                    new Uri($strNormalizedLinkUri),
                    $host,
                    $pathPrefixForVisit,
                    $pathPrefixForSave
                );
            $this->crawlLinks($nextOption, $this->visited);
        }
    }

    public function getSavedCount(): int
    {
        return $this->savedCount;
    }
}
