<?php

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

$csv = new Downloader();

$csv->setData($people);

$csv->download();
exit;
