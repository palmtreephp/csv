<?php

declare(strict_types=1);

namespace Palmtree\Csv\Normalizer;

/**
 * @method static CallableNormalizer create(callable $callback, ?NormalizerInterface $normalizer = null)
 */
class CallableNormalizer extends AbstractNormalizer
{
    /** @var callable */
    private $callback;

    public function __construct(callable $callback, ?NormalizerInterface $normalizer = null)
    {
        $this->setCallback($callback);

        parent::__construct($normalizer);
    }

    public function setCallback(callable $callback): self
    {
        $this->callback = $callback;

        return $this;
    }

    public function getCallback(): callable
    {
        return $this->callback;
    }

    protected function getNormalizedValue(string $value)
    {
        $callback = $this->callback;

        return $callback($value, $this);
    }
}
