<?php

declare(strict_types=1);

namespace Palmtree\Csv\Normalizer;

class CallableNormalizer implements NormalizerInterface
{
    private \Closure $callback;
    private NormalizerInterface $normalizer;

    public function __construct(callable $callback, ?NormalizerInterface $normalizer = null)
    {
        $this->callback = \Closure::fromCallable($callback);
        $this->normalizer = $normalizer ?? new NullNormalizer();
    }

    /** @psalm-suppress UnsafeInstantiation */
    public static function create(callable $callback, ?NormalizerInterface $normalizer = null): self
    {
        return new static($callback, $normalizer);
    }

    public function getCallback(): callable
    {
        return $this->callback;
    }

    /** @return mixed */
    public function normalize(string $value)
    {
        $callback = $this->callback;

        return $callback($this->normalizer->normalize($value), $this);
    }
}
