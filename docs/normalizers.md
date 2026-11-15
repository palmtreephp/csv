# Normalizers

## Normalize Data Types
A number of different normalizers can be used to convert data from strings into certain data types.
Below is contrived example using some of the currently bundled normalizers:

```php
$csv = new Reader('/path/to/products.csv');

$csv->addNormalizers([
    // Convert to integer
    'product_id' => new NumberNormalizer(),

    // Keep data as string but trim it
    'name' => new StringNormalizer(),

    // Convert to float, rounded to 4 decimal places
    'price' => (new NumberNormalizer())->scale(4),

    // Convert to a number, or null if the cell is empty or not numeric
    'stock' => (new NumberNormalizer())->nullable(true),

    // Convert to boolean true or false
    'enabled' => new BooleanNormalizer(),

    // Convert to an array of integers
    'related_product_ids' => new ArrayNormalizer(new NumberNormalizer()),

    // Custom conversion with a callback
    'specials' => new CallableNormalizer(fn ($value) => json_decode($value)),
]);
```
