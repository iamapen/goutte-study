<?php
/**
 * v3 で動くようになってので、綺麗にする
 * @var \Psr\Log\LoggerInterface $logger
 */
declare(strict_types=1);

require __DIR__ . '/../bootstrap/bootstrap.php';

use Acme\v1\Crawler\CrawlOption;
use Acme\v1\Writer\FileWriter;
use Nyholm\Psr7\Uri;

$startUrl = new Uri('https://qiita.com/iamapen/items/3c0ef2825a14a3b68d82');
$includeHost = 'qiita.com';
$includePathPrefixForVisit = '/iamapen';
$includePathPrefixForSave = '/iamapen/items/';

$option = new CrawlOption(
    $startUrl,
    $includeHost,
    $includePathPrefixForVisit,
    $includePathPrefixForSave
);

$outputDir = __DIR__ . '/../tmp/output';
$writer = new FileWriter($outputDir);

$client = new \Symfony\Component\BrowserKit\HttpBrowser();
$crawler = new \Acme\v1\Crawler\Crawler($client, $writer);
$crawler->setLogger($logger);
$crawler->crawlLinks($option);

echo sprintf('%s 件保存しました', $crawler->getSavedCount()), "\n";
