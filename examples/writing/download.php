<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/../vendor/autoload.php';

use Palmtree\Csv\Downloader;

$people   = [];
$people[] = [
    'name'   => 'Alice',
    'age'    => '24',
    'gender' => 'Female',
];
$people[] = [
    'name'   => 'Bob',
    'age'    => '28',
    'gender' => 'Male',
];

Downloader::download('people.csv', $people);

exit;
