# Reading

`Reader` reads one line into memory at a time, so large files can be read without hitting memory limits.

## Reading from a CSV File

```php
$csv = new Reader('people.csv');

foreach($csv as $row) {
    $name = $row['name'];
    if (isset($row['age'])) {
        echo "age is set!";
    }
}
```

`Reader::read('people.csv')` is a shorthand for `new Reader('people.csv')`.

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

To read every row into an array, use `toArray()`. This loads the whole file into memory:

```php
$people = $csv->toArray();
// [['name' => 'Alice', 'age' => '24'], ['name' => 'Bob', 'age' => '28']]
```

Blank lines are skipped.

The iterator key is the row's position in the file, which counts the header row and any blank lines. It isn't a
zero-based index of data rows, so keep your own counter if you need one.

## Rows and Cells

Each row is a `Row` object. It can be used like an array, counted and iterated:

```php
foreach ($csv as $row) {
    $row['name'];        // The cell's value, after normalization
    isset($row['age']);  // Whether the row has the cell
    count($row);         // The number of cells
    $row->toArray();     // ['name' => 'Alice', 'age' => '24']

    foreach ($row as $header => $cell) {
        // $cell is a Cell object
    }
}
```

Reading a cell that doesn't exist throws an `OutOfBoundsException`, so check with `isset()` first when a column is
optional.

`getCell()` returns the `Cell` object, or `null` if there isn't one. A cell holds the raw string from the file and the
[normalizer](normalizers.md) for its column:

```php
$cell = $row->getCell('price');

$cell?->getValue();     // The normalized value, e.g. 99.99
$cell?->getRawValue();  // The string from the file, e.g. '99.99'
(string)$cell;          // The normalized value as a string, or the raw value if it can't be converted
```

## Headers

By default, the first row is read as the headers and used as the keys of every other row. `getHeaders()` returns the
header row, or `null` for a file without headers:

```php
$csv->getHeaders()?->toArray(); // ['name', 'age']
```

Each header must be unique. Reading a file with duplicate headers throws an `UnexpectedValueException`, since one
column would overwrite the other. Read the file [without headers](#no-headers) to access its columns by position
instead.

## No Headers

If your CSV contains no headers pass `false` as the second argument to the constructor:

```php
$csv = new Reader('people.csv', false);

// Alternatively, call the setHasHeaders() method after instantiation:
//$csv->setHasHeaders(false);
```

Rows are then keyed by their zero-based column position:

```php
foreach ($csv as $row) {
    $name = $row[0];
}
```

## Header Offset

If your CSV headers are not on the first row you may specify the (zero based) row offset:

```php
$csv = new Reader('people.csv');
// Headers are on the second row so let's set the offset to 1
$csv->setHeaderOffset(1);
```

## Skipping Rows

`setOffset()` skips that many data rows, after the header row:

```php
$csv = new Reader('people.csv');
// Start reading at the third data row
$csv->setOffset(2);
```

## Byte Order Marks

A UTF-8 byte order mark at the start of the file is stripped, so it doesn't end up in the first header. Pass another of
the `StringUtil::BOM_*` constants to strip a different one, or `null` to leave the file as it is:

```php
use Palmtree\Csv\Util\StringUtil;

$csv->setStripBom(StringUtil::BOM_UTF16_LE);
$csv->setStripBom(null);
```

## Inline Reading

You may use the `InlineReader` to parse a CSV string rather than a file, if it was obtained from an API call or some other means:

```php
$csv = new \Palmtree\Csv\InlineReader('"header_1","header_2"' . "\r\n" . '"foo","bar"');
```

## Reading from a URL

`ExternalReader` downloads a CSV from an `http` or `https` URL to a temporary file, then reads it like any other
file. The download is streamed, so large files aren't loaded into memory. The temporary file is deleted when the
reader is destroyed.

```php
$csv = new \Palmtree\Csv\ExternalReader('https://example.com/people.csv');
```

Other URL schemes and local paths throw an `InvalidArgumentException`, so a URL from user input can't be used to read
local files. Use `Reader` for local files.

Pass a stream context as the third argument to set a timeout, request headers or other
[HTTP options](https://www.php.net/manual/en/context.http.php):

```php
$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'header' => 'Authorization: Bearer ' . $token,
    ],
]);

$csv = new \Palmtree\Csv\ExternalReader('https://example.com/people.csv', true, $context);
```

See [CSV Control](csv-control.md) for files that use a different delimiter, enclosure or escape character.
