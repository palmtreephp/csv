# CSV Control

## CSV Control
You can access the document object to change the CSV delimiter, enclosure and escape character:

```php
$csv = new Reader('/path/to/input.csv');

$csv->setDelimiter("\t");
$csv->setEnclosure('"');
$csv->setEscapeCharacter("\\");
```

## Fine-grained Control
The document object extends PHP's [SplFileObject](https://www.php.net/manual/en/class.splfileobject.php) and inherits its methods:

```php
$csv = new Reader('/path/to/input.csv');
$csv->getDocument()->setFlags(\SplFileObject::DROP_NEW_LINE);
```
