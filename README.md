# Palmtree CSV

A CSV parser and builder for PHP.

The `CsvParser` class implements the `Iterator` interface meaning large files can be parsed
without hitting any memory limits because only one line is loaded at a time.

## Requirements
* PHP >= 5.3

## Usage

#### Building a CSV file for download

```php
<?php
use Palmtree\Csv;
$csv = new Csv( 'people.csv' );
$csv->addHeaders( [ 'name', 'age', 'gender' ] );

$csv->addRow( [ 'Alice', '24', 'Female'] );
$csv->addRow( [ 'Bob', '28', 'Male' ] );

$csv->download();
```

#### Parsing a CSV file into an array
```php
<?php
use Palmtree\Csv\CsvParser;
$csv = new CsvParser( 'people.csv' );

foreach( $csv as $row ) {
	// Alice is a 24 year old female.
	echo "{$row['name']} is a {$row['age']} year old {$row['gender']}";
}
```

## Public Properties
