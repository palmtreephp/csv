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
    'price' => (new NumberNormalizer())->scale(4),

    // Convert to a number, or null if the cell is empty or not numeric
    'stock' => (new NumberNormalizer())->nullable(true),

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

#### Framework Responses
`Downloader::sendResponse()` and `Downloader::download()` call `header()` and write straight to the output. To return a
response from a framework instead, use the downloader's response headers and `writeTo()`, which copies the CSV to a
stream in chunks.

Symfony:

```php
use Symfony\Component\HttpFoundation\StreamedResponse;

$csv = new Downloader('people.csv');
$csv->setData($people);

return new StreamedResponse(
    fn () => $csv->writeTo(fopen('php://output', 'w')),
    200,
    $csv->getResponseHeaders(),
);
```

PSR-7, with any PSR-17 response and stream factory:

```php
$csv = new Downloader('people.csv');
$csv->setData($people);

$stream = fopen('php://temp', 'w+');
$csv->writeTo($stream);
rewind($stream);

$response = $responseFactory->createResponse()
    ->withBody($streamFactory->createStreamFromResource($stream));

foreach ($csv->getResponseHeaders() as $name => $value) {
    $response = $response->withHeader($name, $value);
}

return $response;
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

#### Writing Large Data Sets

`Writer::write()`, `Downloader::download()`, `setData()` and `addRows()` accept any iterable. Rows are written
as they are iterated, so a generator can export a large data set without loading it all into memory:

```php
function people(PDO $pdo): Generator
{
    $statement = $pdo->query('SELECT name, age FROM people');

    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        yield $row;
    }
}

Writer::write('/path/to/output.csv', people($pdo));
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

#### Formula Escaping
Spreadsheet applications evaluate cells that start with `=`, `+`, `-` or `@` as formulas, so a CSV built from user
input can run formulas when it's opened ([CSV injection](https://owasp.org/www-community/attacks/CSV_Injection)).
Enable formula escaping to prefix those headers and values with a single quote, so they're shown as text:

```php
$csv = new Downloader('export.csv');
$csv->setEscapeFormulas(true);
```

Integers and floats aren't escaped, so negative numbers stay numeric. Strings such as `'-5'` are escaped, so pass
numbers as `int` or `float` if they should stay numeric. Only enable this for files meant for spreadsheets: the quote
becomes part of the value for any other program reading the file.

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
