# Palmtree CSV Class

A CSV parser and builder for PHP.

## Usage

#### Building a CSV file for download.

```php
<?php
use Palmtree\Csv;
$csv = new Csv();
$csv->addHeaders( ['name', 'age', 'gender'] );

$csv->addRow( ['Alice', '24', 'Female'] );
$csv->addRow( ['Bob', '28', 'Male'] );

$csv->filename = 'people';

$csv->download();
```

#### Parsing a CSV file into an array.
```php
<?php
use Palmtree\Csv;
$csv = new Csv();
$rows = $csv->parseFile( 'people.csv', true );

foreach( $rows as $row ) {
	// Alice is a 24 year old female.
	echo "{$row['name']} is a {$row['age']} year old {$row['gender']}";
}
```
