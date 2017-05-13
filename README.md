# Palmtree CSV

[![License](http://img.shields.io/packagist/l/palmtree/canonical-url-bundle.svg)](LICENSE)
[![Travis](https://img.shields.io/travis/palmtreephp/csv.svg)](https://travis-ci.org/palmtreephp/csv)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/palmtreephp/csv.svg)](https://scrutinizer-ci.com/g/palmtreephp/csv/)
[![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/palmtreephp/csv.svg)](https://scrutinizer-ci.com/g/palmtreephp/csv/)

A CSV reader and writer for PHP.

The `Reader` class implements the `Iterator` interface meaning large files can be parsed
without hitting any memory limits because only one line is loaded at a time.

## Requirements
* PHP >= 5.6

## Installation

Use composer to add the package to your dependencies:
```bash
composer require palmtree/csv
```

## Configuration

If you're trying to read a CSV file in or generated by an old Mac computer you may need to include
the following snippet before creating a new `Reader` instance:

```php
<?php
if (!ini_get('auto_detect_line_endings')) {
    ini_set('auto_detect_line_endings', '1');
}
```

This is because Macs used to use `\r` as a line separator. See [here](http://php.net/manual/en/function.fgetcsv.php#refsect1-function.fgetcsv-returnvalues) for more details

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

Downloader::download('people.csv', $people);
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

#### Normalizing data types

A number of different normalizers can be used to convert data from strings into certain data types.
Below is contrived example using all currently bundle normalizers:
```php
<?php
use Palmtree\Csv\Reader;
use Palmtree\Csv\Normalizer as Normalizer;

$csv = new Reader(__DIR__ . '/../products.csv');

$integerNormalizer = (new Normalizer\NumberNormalizer())->setDecimals(0);

$csv->addNormalizers([
     // Convert to integer
    'product_id'          => $integerNormalizer,
    
    // Keep data as string but trim it
    'name'                => new Normalizer\StringNormalizer(),
     
     // Convert to float
    'price'               => (new Normalizer\NumberNormalizer())->setDecimals(4),
     
     // Convert to integer
    'quantity'            => $integerNormalizer,
    
    // Convert to boolean true or false
    'enabled'             => new Normalizer\BooleanNormalizer(),
    
    // Convert to an array of numbers
    'related_product_ids' => new Normalizer\ArrayNormalizer($integerNormalizer),
    
    // Custom conversion with a callback
    'specials'            => new Normalizer\CallableNormalizer(function ($value) {
        return json_decode($value, true);
    }),
]);
```

See the [examples](examples) directory for more usage examples.

## License

Released under the [MIT license](LICENSE)
