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

    /**
     * @return self
     */
    public function setCallback(callable $callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @return callable
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    protected function getNormalizedValue($value)
    {
        $value = \call_user_func($this->getCallback(), $value, $this);

        return $value;
    }
}
