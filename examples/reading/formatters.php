<?php

require_once dirname(__DIR__) . '/../vendor/autoload.php';

use Palmtree\Csv\Cell\Cell;
use Palmtree\Csv\Formatter\ArrayFormatter;
use Palmtree\Csv\Formatter\BooleanFormatter;
use Palmtree\Csv\Formatter\CallableFormatter;
use Palmtree\Csv\Formatter\NumberFormatter;
use Palmtree\Csv\Formatter\StringFormatter;
use Palmtree\Csv\Reader;

$csv = new Reader(__DIR__ . '/../products.csv');

//setlocale(LC_MONETARY, '');
$csv->addFormatters([
    'product_id'          => new NumberFormatter(),
    'name'                => new StringFormatter(),
    'price'               => (new NumberFormatter())->setDecimals(4),
    'quantity'            => new NumberFormatter(),
    'enabled'             => new BooleanFormatter(),
    'related_product_ids' => new ArrayFormatter(new NumberFormatter()),
    'specials'            => new CallableFormatter(function ($value) {
        return json_decode($value, true);
    }),
]);

// Iterate over the object directly:
/**
 * @var mixed  $key
 * @var Cell[] $row
 */
foreach ($csv as $key => $row) {
    foreach ($row as $cellKey => $cell) {
        echo "$cellKey: ";
        var_export($cell->getValue());
        echo "\n";
    }
    echo "----\n";
}
