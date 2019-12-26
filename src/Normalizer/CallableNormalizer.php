<?php

namespace Palmtree\Csv\Normalizer;

class CallableNormalizer extends AbstractNormalizer
{
    /** @var callable */
    private $callback;

    public function __construct(callable $callback, NormalizerInterface $normalizer = null)
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

    /**
     * @param $value
     *
     * @return mixed
     */
    protected function getNormalizedValue(string $value)
    {
        $value = \call_user_func($this->getCallback(), $value, $this);

        return $value;
    }
}
