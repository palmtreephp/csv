<?php

declare(strict_types=1);

namespace Palmtree\Csv\Normalizer;

class CallableNormalizer implements NormalizerInterface
{
    private \Closure $callback;
    private NormalizerInterface $normalizer;

    public function __construct(callable $callback, ?NormalizerInterface $normalizer = null)
    {
        $this->callback = $callback(...);
        $this->normalizer = $normalizer ?? new NullNormalizer();
    }

    public function normalize(string $value): mixed
    {
        $callback = $this->callback;

        return $callback($this->normalizer->normalize($value), $this);
    }
}
