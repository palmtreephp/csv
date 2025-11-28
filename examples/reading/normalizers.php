<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Palmtree\Csv\Cell\Cell;
use Palmtree\Csv\Normalizer;
use Palmtree\Csv\Reader;

$csv = new Reader(__DIR__ . '/../products.csv');

$csv->addNormalizers([
    'product_id' => new Normalizer\NumberNormalizer(),
    'name' => new Normalizer\StringNormalizer(),
    'price' => (new Normalizer\NumberNormalizer())->scale(4),
    'quantity' => new Normalizer\NumberNormalizer(),
    'enabled' => (new Normalizer\BooleanNormalizer())->pairs(['yes' => 'no']),
    'related_product_ids' => new Normalizer\ArrayNormalizer(new Normalizer\NumberNormalizer()),
    'description' => new Normalizer\HtmlNormalizer(),
    'specials' => new Normalizer\CallableNormalizer(fn (string $value) => json_decode($value), new Normalizer\BooleanNormalizer()),
]);

/**
 * @var mixed       $key
 * @var array<Cell> $row
 */
foreach ($csv as $row) {
    foreach ($row as $key => $value) {
        echo "$key: ";
        echo var_format($row[$key]);
        echo \PHP_EOL;
    }
}

/**
 * Returns a string representation of a variable of different data types.
 */
function var_format($var): string
{
    if (is_array($var) || is_object($var)) {
        return print_r($var, true);
    }

    if ($var === null || is_bool($var)) {
        return $var === null ? 'null' : ($var ? 'true' : 'false');
    }

    $output = $var;

    if (is_string($output)) {
        $output = "'$output'";
    }

    return $output;
}
