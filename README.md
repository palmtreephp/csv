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

```bash
composer require palmtree/csv
```

## Usage

### Reading

#### Reading from a CSV File

```php
$csv = new Reader('people.csv');

foreach($csv as $row) {
    $name = $row['name'];
    if (isset($row['age'])) {
        echo "age is set!";
    }
}
```

Rows can also be destructured by position:

> [!NOTE]
> Positional destructuring depends on column order, so prefer `$row['name']` for files you don't
> control.

```php
foreach($csv as [$name, $age]) {
    // ...
}
```

This doesn't work if any header is an integer (e.g. a column named `1`).

#### Normalize Data Types
A number of different normalizers can be used to convert data from strings into certain data types.
Below is contrived example using some of the currently bundled normalizers:

```php
$csv = new Reader('/path/to/products.csv');

$csv->addNormalizers([
    // Convert to integer
    'product_id' => new NumberNormalizer(),

    // Keep data as string but trim it
    'name' => new StringNormalizer(),

    // Convert to float, rounded to 4 decimal places
    'price' => NumberNormalizer::create()->scale(4),

    // Convert to boolean true or false
    'enabled' => new BooleanNormalizer(),

    // Convert to an array of integers
    'related_product_ids' => new ArrayNormalizer(new NumberNormalizer()),

    // Custom conversion with a callback
    'specials' => new CallableNormalizer(fn ($value) => json_decode($value)),
]);
```

#### No Headers
If your CSV contains no headers pass `false` as the second argument to the constructor:

```php
$csv = new Reader('people.csv', false);

// Alternatively, call the setHasHeaders() method after instantiation:
//$csv->setHasHeaders(false);

```

#### Header Offset
If your CSV headers are not on the first row you may specify the (zero based) row offset:

```php
$csv = new Reader('people.csv');
// Headers are on the second row so let's set the offset to 1
$csv->setHeaderOffset(1);
```

#### Inline Reading
You may use the `InlineReader` to parse a CSV string rather than a file, if it was obtained from an API call or some other means:

```php
$csv = new \Palmtree\Csv\InlineReader('"header_1","header_2"' . "\r\n" . '"foo","bar"');
```

### Writing

#### Build and Download a CSV File

```php
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

Downloader::download('filename.csv', $people);
```

#### Writing to a CSV File

```php
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


Writer::write('/path/to/output.csv', $people);
```

See the [examples](examples) directory for more usage examples.

## Advanced Usage

#### CSV Control
You can access the document object to change the CSV delimiter, enclosure and escape character:

```php
$csv = new Reader('/path/to/input.csv');

$csv->setDelimiter("\t");
$csv->setEnclosure('"');
$csv->setEscapeCharacter("\\");
```

#### Line Endings
CSVs default to `\r\n` line endings. Access the document object if you need to change this:

```php
$csv = new Writer('/path/to/output.csv');
$csv->getDocument()->setLineEnding("\n");
```


#### Fine-grained Control
The document object extends PHP's [SplFileObject](https://www.php.net/manual/en/class.splfileobject.php) and inherits its methods:

```php
$csv = new Reader('/path/to/input.csv');
$csv->getDocument()->setFlags(\SplFileObject::DROP_NEW_LINE);
```

## License
Released under the [MIT license](LICENSE)
