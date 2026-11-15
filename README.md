# Palmtree CSV

[![License](https://img.shields.io/packagist/l/palmtree/csv.svg)](LICENSE)
[![Latest Version](https://img.shields.io/packagist/v/palmtree/csv.svg)](https://packagist.org/packages/palmtree/csv)
[![PHP Version](https://img.shields.io/packagist/dependency-v/palmtree/csv/php.svg)](https://packagist.org/packages/palmtree/csv)
[![Build Status](https://github.com/palmtreephp/csv/actions/workflows/build.yaml/badge.svg?branch=master)](https://github.com/palmtreephp/csv/actions/workflows/build.yaml)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg)](phpstan.dist.neon)

A CSV reader and writer for PHP.

The `Reader` class implements the `Iterator` interface, loading one line into memory at a time. This means large files can be parsed
without hitting any memory limits.

## Requirements
* PHP >= 8.3

## Installation
Use composer to add the package to your dependencies:

```console
composer require palmtree/csv
```

## Usage

```php
use Palmtree\Csv\Reader;
use Palmtree\Csv\Writer;

foreach (new Reader('people.csv') as $row) {
    echo $row['name'];
}

Writer::write('people.csv', [
    ['name' => 'Alice', 'age' => 24],
    ['name' => 'Bob', 'age' => 28],
]);
```

## Documentation

See the [documentation index](docs/index.md) for every topic, or jump to a page:

* [Reading](docs/reading.md)
* [Normalizers](docs/normalizers.md)
* [Writing](docs/writing.md)
* [Downloads](docs/downloads.md)
* [CSV Control](docs/csv-control.md)

See the [examples](examples) directory for more usage examples.

## License
Released under the [MIT license](LICENSE)
