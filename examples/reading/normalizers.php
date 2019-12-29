<?php

require_once dirname(__DIR__) . '/../vendor/autoload.php';

use Palmtree\Csv\Cell\Cell;
use Palmtree\Csv\Normalizer as Normalizer;
use Palmtree\Csv\Reader;

$csv = new Reader(__DIR__ . '/../products.csv');

$csv->addNormalizers([
        'product_id'          => new Normalizer\NumberNormalizer(),
        'name'                => new Normalizer\StringNormalizer(),
        'price'               => Normalizer\NumberNormalizer::create()->setDecimals(4),
        'quantity'            => new Normalizer\NumberNormalizer(),
        'enabled'             => Normalizer\BooleanNormalizer::create()->setPairs(['yes' => 'no'])->setNullable(true),
        'related_product_ids' => new Normalizer\ArrayNormalizer(new Normalizer\NumberNormalizer()),
        'description'         => new Normalizer\HtmlNormalizer(),
        'specials'            => new Normalizer\CallableNormalizer(function (string $value) {
            // $normalizer is the CallableNormalizer instance.
            return \json_decode($value, true);
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
