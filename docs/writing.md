# Writing

## Writing to a CSV File

`Writer::write()` writes an array of rows to a file, using the first row's keys as headers:

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

```csv
"name","age","gender"
"Alice","24","Female"
"Bob","28","Male"
```

Every value is enclosed in double quotes, and double quotes inside values are escaped by doubling them, as
[RFC 4180](https://datatracker.ietf.org/doc/html/rfc4180) describes. The file doesn't end with a line ending.

To build a file row by row, create a `Writer`. The file is created, or emptied if it exists, when the first row or
header is written:

```php
$csv = new Writer('/path/to/output.csv');

$csv->addRow(['name' => 'Alice', 'age' => 24]);
$csv->addRows($morePeople);

$csv->closeDocument();
```

`closeDocument()` writes any pending headers and closes the file. It's also called when the writer is destroyed.

## Values

Values can be strings, integers, floats, booleans, `null` or `Stringable` objects. `true` is written as `1`, and
`false` and `null` are written as empty cells. Any other value, including a nested array, throws an
`InvalidArgumentException`.

## Headers

Headers are taken from the keys of the first row, or can be set before adding rows:

```php
$csv = new Writer('/path/to/output.csv');
$csv->setHeaders(['name', 'age']);
$csv->addHeader('email');
```

Rows with string keys are written in header order, whatever order their keys are in. A missing key is written as an
empty cell, and a key that isn't a header throws an `InvalidArgumentException`. Rows without string keys are written
as they are.

Headers are written just before the first row, or when the contents are read or the file is closed, so they can be
changed until then. Changing them afterwards throws a `LogicException`.

To write a file without headers, pass `false` as the second argument:

```php
$csv = new Writer('/path/to/output.csv', false);
$csv->addRow(['Alice', 24]);
```

## Reading the Contents

`getContents()` returns everything written so far as a string:

```php
$csv = new Writer('php://temp');
$csv->setData($people);

$string = $csv->getContents();
```

`writeTo()` copies the contents to a stream in chunks instead, so large files aren't loaded into memory. It returns the
number of bytes written:

```php
$csv->writeTo(fopen('/path/to/copy.csv', 'w'));
```

More rows can be added after either.

## Writing Large Data Sets

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

## Formula Escaping

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

## Line Endings

Rows are separated by `\r\n`. Set the line ending on the document if you need something else:

```php
$csv = new Writer('/path/to/output.csv');
$csv->getDocument()->setLineEnding("\n");
```

See [CSV Control](csv-control.md) to write with a different delimiter or enclosure, and [Downloads](downloads.md) to
send a CSV to the browser.
