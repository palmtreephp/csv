# Palmtree CSV

[![License](http://img.shields.io/packagist/l/palmtree/csv.svg)](LICENSE)
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

#### Read a CSV file
```php
<?php
use Palmtree\Csv\Reader;

$csv = new Reader('people.csv');

foreach($csv as $row) {
	echo "{$row['name']} is a {$row['age']} year old {$row['gender']}";
}
```

#### Normalize data types

A number of different normalizers can be used to convert data from strings into certain data types.
Below is contrived example using all currently bundled normalizers:
```php
<?php
use Palmtree\Csv\Reader;
use Palmtree\Csv\Normalizer as Normalizer;

$csv = new Reader(__DIR__ . '/../products.csv');

// Create a NumberNormalizer instance which rounds to 0 decimals
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

#### No Headers
If your CSV contains no headers:

```php
<?php
use Palmtree\Csv\Reader;

// Pass `false` as the second constructor argument to treat the first row as data
$csv = new Reader('people.csv', false);

// Alternatively, call the setHasHeaders() method after instantiation:
//$csv->setHasHeaders(false);

```

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

See the [examples](examples) directory for more usage examples.

## Advanced Usage

#### CSV Control

You can access the document object to change the CSV delimiter, enclosure and escape character:
```php
<?php
use Palmtree\Csv\Reader;

$csv = new Reader('people.csv');

$csv->getDocument()->setDelimiter("\t");
$csv->getDocument()->setEnclosure('"');
$csv->getDocument()->setEscapeChar("\\");
```

#### Line Endings
CSVs default to `\r\n` line endings. Access the document object if you need to change this:

```php
<?php
use Palmtree\Csv\Writer;

$csv = new Writer('people.csv');
$csv->getDocument()->setLineEnding("\n");
```


#### Fine-grained Control
The document object extends PHP's [SplFileObject](http://php.net/manual/en/class.splfileobject.php) and inherits it's methods:

```php
<?php
use Palmtree\Csv\Reader;

$csv = new Reader('people.csv');
$csv->getDocument()->setFlags(\SplFileObject::DROP_NEW_LINE);
```

## License

Released under the [MIT license](LICENSE)
