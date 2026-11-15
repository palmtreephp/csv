# Normalizers

Every value in a CSV file is a string. Normalizers convert them into other types, such as numbers, booleans and dates,
or clean them up, such as trimming whitespace.

Add a normalizer for a column by its header, or by its zero-based position for a file without headers. Columns without
one return the string from the file.

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

// Or one at a time
$csv->addNormalizer('created_at', new DateTimeNormalizer());
```

Values are normalized each time they're read, so `$row['price']` runs the normalizer on every access.

All normalizers are in the `Palmtree\Csv\Normalizer` namespace.

## Bundled Normalizers

### NumberNormalizer

Converts numeric strings to an `int` or a `float`, using PHP's [numeric string](https://www.php.net/manual/en/language.types.numeric-strings.php)
rules, so `'0012'` becomes `12` and `'1.5'` becomes `1.5`.

| Option | Default | Effect |
|---|---|---|
| `scale(?int $scale)` | `null` | Rounds to this many decimal places. Rounding always returns a `float`. |
| `nullable(bool $nullable)` | `false` | Returns `null` for non-numeric values, including empty cells. Otherwise they return `0`. |

### StringNormalizer

Trims whitespace from both ends of the value.

| Option | Default | Effect |
|---|---|---|
| `trim(bool $trim)` | `true` | Whether to trim at all. |
| `trimChars(array $chars)` | Space, `\t`, `\n`, `\r`, `\0`, `\x0B` | Replaces the characters to trim. |
| `addTrimChar(string $char)` | | Adds a character to trim. |

```php
// Trim whitespace and quotes
$normalizer = (new StringNormalizer())->addTrimChar("'");
```

### BooleanNormalizer

Converts a value to `true` or `false`. The value is trimmed, then compared against pairs of truthy and falsey strings.
The default pairs are `true`/`false`, `1`/`0`, `on`/`off`, `yes`/`no` and `enabled`/`disabled`.

| Option | Default | Effect |
|---|---|---|
| `pairs(array $pairs)` | The pairs above | Replaces the pairs, with truthy strings as keys and falsey strings as values. |
| `addPair(string $truthy, string $falsey)` | | Adds a pair. Unlike the other options, it doesn't return the normalizer, so it can't be chained. |
| `caseSensitive(bool $caseSensitive)` | `false` | Whether `'Yes'` must match the case of `'yes'`. |
| `nullable(bool $nullable)` | `false` | Returns `null` for values that aren't in any pair. Otherwise they return `false`. |

```php
$normalizer = (new BooleanNormalizer())
    ->pairs(['y' => 'n'])
    ->nullable(true);

$normalizer->normalize('Y'); // true
$normalizer->normalize('n'); // false
$normalizer->normalize('');  // null
```

### DateTimeNormalizer

Converts a value to a `DateTime` with [`DateTime::createFromFormat()`](https://www.php.net/manual/en/datetime.createfromformat.php).
Returns `null` if the value doesn't match the format or isn't a real date, such as `2024-02-30`.

| Option | Default | Effect |
|---|---|---|
| `format(string $format)` | `Y-m-d` | The format to parse. Fields not in the format are set to zero, or the Unix epoch, rather than the current time. |

```php
$normalizer = (new DateTimeNormalizer())->format('d/m/Y H:i');
```

### ArrayNormalizer

Splits a value into an array. The delimiter and whitespace are trimmed from both ends of the value first, so
`'1,2,3,'` becomes `['1', '2', '3']`.

The normalizer passed to the constructor is applied to each item, rather than to the whole value:

```php
// '1, 2, 3' becomes [1, 2, 3]
$normalizer = new ArrayNormalizer(new NumberNormalizer());
```

| Option | Default | Effect |
|---|---|---|
| `delimiter(string $delimiter)` | `,` | The string to split on. |

### HtmlNormalizer

Encodes HTML special characters with [`htmlentities()`](https://www.php.net/manual/en/function.htmlentities.php), so
values can be output in HTML safely.

| Option | Default | Effect |
|---|---|---|
| `encode(bool $encode)` | `true` | Set to `false` to decode entities with `html_entity_decode()` instead. |
| `flags(int $flags)` | `ENT_QUOTES` | The flags passed to `htmlentities()` or `html_entity_decode()`. |

### CallableNormalizer

Passes the value to a callback and returns the result. The callback receives the value and the normalizer:

```php
$normalizer = new CallableNormalizer(fn (string $value) => strtoupper($value));
```

### NullNormalizer

Returns the value unchanged. This is the default for columns without a normalizer.

## Combining Normalizers

Every normalizer except `NullNormalizer` takes another normalizer as a constructor argument: the first argument, or
the second for `CallableNormalizer`. The inner normalizer
runs first, and its result is passed to the outer one. Except for `CallableNormalizer`, the inner normalizer must
return a string, or an `UnexpectedValueException` is thrown.

```php
// Trim quotes before parsing the number
$normalizer = new NumberNormalizer((new StringNormalizer())->addTrimChar('"'));
```

`ArrayNormalizer` is different: its inner normalizer runs on [each item](#arraynormalizer).

## Default Normalizer

`setDefaultNormalizer()` sets the normalizer class used for columns without their own normalizer. It must be a class
name, and is created with no constructor arguments:

```php
// Trim every column that doesn't have its own normalizer
$csv->setDefaultNormalizer(StringNormalizer::class);
```

## Header Normalizer

`setHeaderNormalizer()` normalizes the header row before its values are used as the keys of every other row. It
doesn't apply to data rows:

```php
// Trim headers and convert them to snake_case, so ' First Name' becomes 'first_name'
$csv->setHeaderNormalizer(new CallableNormalizer(
    fn (string $header) => strtolower(str_replace(' ', '_', $header)),
    new StringNormalizer(),
));
```

Normalizers added with `addNormalizer()` use the normalized headers as keys, so add them as `'first_name'` above.

## Writing a Normalizer

Implement `NormalizerInterface`:

```php
use Palmtree\Csv\Normalizer\NormalizerInterface;

class CentsNormalizer implements NormalizerInterface
{
    public function normalize(string $value): int
    {
        return (int)round((float)$value * 100);
    }
}
```

Or extend `AbstractNormalizer` and implement `getNormalizedValue()`, which also lets your normalizer take an inner
normalizer, like the bundled ones:

```php
use Palmtree\Csv\Normalizer\AbstractNormalizer;

class CentsNormalizer extends AbstractNormalizer
{
    protected function getNormalizedValue(string $value): int
    {
        return (int)round((float)$value * 100);
    }
}

$normalizer = new CentsNormalizer(new StringNormalizer());
```
