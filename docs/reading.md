# Reading

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

## No Headers
If your CSV contains no headers pass `false` as the second argument to the constructor:

```php
$csv = new Reader('people.csv', false);

// Alternatively, call the setHasHeaders() method after instantiation:
//$csv->setHasHeaders(false);

```

## Header Offset
If your CSV headers are not on the first row you may specify the (zero based) row offset:

```php
$csv = new Reader('people.csv');
// Headers are on the second row so let's set the offset to 1
$csv->setHeaderOffset(1);
```

## Inline Reading
You may use the `InlineReader` to parse a CSV string rather than a file, if it was obtained from an API call or some other means:

```php
$csv = new \Palmtree\Csv\InlineReader('"header_1","header_2"' . "\r\n" . '"foo","bar"');
```
