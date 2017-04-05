<?php

require_once dirname(__DIR__) . '../vendor/autoload.php';

use Palmtree\Csv\Cell\Cell;
use Palmtree\Csv\Formatter\ArrayFormatter;
use Palmtree\Csv\Formatter\BooleanFormatter;
use Palmtree\Csv\Formatter\FilterFormatter;
use Palmtree\Csv\Formatter\MoneyFormatter;
use Palmtree\Csv\Formatter\NumberFormatter;
use Palmtree\Csv\Formatter\StringFormatter;
use Palmtree\Csv\Reader;

$csv = new Reader(__DIR__ . '/test.csv');

setlocale(LC_MONETARY, '');
$csv->addFormatters([
    'product_ids' => new ArrayFormatter(new NumberFormatter(), ','),
    'age'         => new NumberFormatter(new StringFormatter(), 0),
    //'price'       => new MoneyFormatter(),
    'price'       => new NumberFormatter(4),
    //'enabled'     => new BooleanFormatter(),
    'enabled'     => new NumberFormatter(new BooleanFormatter()),
    'specials'    => new FilterFormatter(function ($value) {
        return json_decode($value, true);
    }),
    'name'        => new FilterFormatter(function ($value) {
        return strtoupper($value);
    }),
]);

// Iterate over the object directly:
/**
 * @var mixed  $key
 * @var Cell[] $row
 */
foreach ($csv as $key => $row) {
    //var_dump($row['price']->getValue());
    //var_dump($row['enabled']->getValue());
    var_dump($row['product_ids']->getValue());
    /*foreach ($row['product_ids'] as $product_id) {
        var_dump($product_id);
    }*/
}
exit;
