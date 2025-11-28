<?php

declare(strict_types=1);

namespace Palmtree\Csv\Normalizer;

class CallableNormalizer implements NormalizerInterface
{
    private readonly \Closure $callback;

    public function __construct(callable $callback, private readonly ?NormalizerInterface $normalizer = new NullNormalizer())
    {
        $this->callback = $callback(...);
    }

    public function normalize(string $value): mixed
    {
        $callback = $this->callback;

        return $callback($this->normalizer->normalize($value), $this);
    }
}
