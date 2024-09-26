<?php
declare(strict_types=1);

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$logger = new Logger('app');

// STDOUT
$logger->pushHandler(
    new StreamHandler(
        'php://stdout',
//        \Monolog\Level::Debug
        \Monolog\Level::Info
    )
);
return $logger;
