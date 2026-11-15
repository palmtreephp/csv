# Writing

## Writing to a CSV File

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
CSVs default to `\r\n` line endings. Access the document object if you need to change this:

```php
$csv = new Writer('/path/to/output.csv');
$csv->getDocument()->setLineEnding("\n");
```
