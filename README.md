# Palmtree CSV

[![License](http://img.shields.io/packagist/l/palmtree/csv.svg)](LICENSE)
[![Build Status](https://img.shields.io/scrutinizer/build/g/palmtreephp/csv)](https://scrutinizer-ci.com/g/palmtreephp/csv/build-status/master)
[![Code Quality](https://img.shields.io/scrutinizer/quality/g/palmtreephp/csv)](https://scrutinizer-ci.com/g/palmtreephp/csv/)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/palmtreephp/csv.svg)](https://scrutinizer-ci.com/g/palmtreephp/csv/code-structure/master/code-coverage)

A CSV reader and writer for PHP.

The `Reader` class implements the `Iterator` interface, loading one line into memory at a time. This means large files can be parsed
without hitting any memory limits.

## Requirements
* PHP >= 7.4

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
The document object extends PHP's [SplFileObject](http://php.net/manual/en/class.splfileobject.php) and inherits its methods:

```php
$csv = new Reader('/path/to/input.csv');
$csv->getDocument()->setFlags(\SplFileObject::DROP_NEW_LINE);
```

## Configuration
If you're trying to read a CSV file in or generated by an old Mac computer you may need to include
the following snippet before creating a new `Reader` instance:

```php
if (!ini_get('auto_detect_line_endings')) {
    ini_set('auto_detect_line_endings', '1');
}
```

This is because Macs used to use `\r` as a line separator. See [here](http://php.net/manual/en/function.fgetcsv.php#refsect1-function.fgetcsv-returnvalues) for more details.

## License
Released under the [MIT license](LICENSE)
