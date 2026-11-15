# CSV Control

## Delimiter, Enclosure and Escape Character

Readers and writers default to a `,` delimiter, a `"` enclosure and no escape character. Change them with the setters
on the reader or writer:

```php
$csv = new Reader('/path/to/input.tsv');

$csv->setDelimiter("\t");
$csv->setEnclosure("'");
$csv->setEscapeCharacter("\\");
```

Without an escape character, an enclosure inside a value is escaped by doubling it (`""`), as
[RFC 4180](https://datatracker.ietf.org/doc/html/rfc4180) describes. Set an escape character only to read files that
escape enclosures another way, e.g. `\"`. Writers always double the enclosure, and ignore the escape character.

## The Document Object

Readers and writers read and write through a `CsvFileObject`, which extends PHP's
[SplFileObject](https://www.php.net/manual/en/class.splfileobject.php) and inherits its methods.
`getDocument()` returns it, opening the file if it isn't open yet:

```php
$csv = new Reader('/path/to/input.csv');
$csv->getDocument()->getRealPath();
```

`CsvFileObject` adds `setLineEnding()` for [writing](writing.md#line-endings), and `getBytesWritten()`.

> [!WARNING]
> `Reader` depends on the `READ_CSV`, `READ_AHEAD`, `SKIP_EMPTY` and `DROP_NEW_LINE` flags it sets on the document.
> Replacing them with `setFlags()` stops rows from being read.
