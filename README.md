# Palmtree CSV

[![License](http://img.shields.io/packagist/l/palmtree/canonical-url-bundle.svg)](LICENSE)
[![Travis](https://img.shields.io/travis/palmtreephp/csv.svg)](https://travis-ci.org/palmtreephp/csv)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/palmtreephp/csv.svg)](https://scrutinizer-ci.com/g/palmtreephp/csv/)
[![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/palmtreephp/csv.svg)](https://scrutinizer-ci.com/g/palmtreephp/csv/)

A CSV reader and writer for PHP.

The `Reader` class implements the `Iterator` interface meaning large files can be parsed
without hitting any memory limits because only one line is loaded at a time.

## Requirements
* PHP >= 5.3

## Installation

Use composer to add the package to your dependencies:
```bash
composer require palmtree/csv
```

## Usage

#### Build and Download a CSV file
```php
<?php
use Palmtree\Csv\Downloader;

$people   = [];
$people[] = [
    'name'  => 'Alice',
    'age'   => '24',
    'gender' => 'Female',
];
$people[] = [
    'name'  => 'Bob',
    'age'   => '28',
    'gender' => 'Male',
];

$downloader = new Downloader('people.csv');
$downloader->download();
```

#### Write a CSV file

```php
<?php
use Palmtree\Csv\Writer;

$people   = [];
$people[] = [
    'name'  => 'Alice',
    'age'   => '24',
    'gender' => 'Female',
];
$people[] = [
    'name'  => 'Bob',
    'age'   => '28',
    'gender' => 'Male',
];


Writer::write('people.csv', $people);
```

#### Read a CSV file
```php
<?php
use Palmtree\Csv\Reader;

$csv = new Reader('people.csv');

foreach($csv as $row) {
	echo "{$row['name']} is a {$row['age']} year old {$row['gender']}";
}
```

See the [examples](examples) directory for more usage examples.
