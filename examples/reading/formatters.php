<?php

require_once dirname(__DIR__) . '/../vendor/autoload.php';

use Palmtree\Csv\Reader;
use Palmtree\Csv\Cell\Cell;
use Palmtree\Csv\Formatter as Formatter;

$csv = new Reader(__DIR__ . '/../products.csv');

$csv->addFormatters([
    'product_id'          => new Formatter\NumberFormatter(),
    'name'                => new Formatter\StringFormatter(),
    'price'               => (new Formatter\NumberFormatter())->setDecimals(4),
    'quantity'            => new Formatter\NumberFormatter(),
    'enabled'             => new Formatter\BooleanFormatter(),
    'related_product_ids' => new Formatter\ArrayFormatter(new Formatter\NumberFormatter()),
    'specials'            => new Formatter\CallableFormatter(function ($value) {
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
