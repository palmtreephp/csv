<?php

namespace Palmtree\Csv\Normalizer;

class CallableNormalizer extends AbstractNormalizer
{
    protected $callback;

    /**
     * CallableNormalizer constructor.
     *
     * @param callable                 $callback
     * @param NormalizerInterface|null $normalizer
     */
    public function __construct(callable $callback, NormalizerInterface $normalizer = null)
    {
        $this->setCallback($callback);

        parent::__construct($normalizer);
    }

    /**
     * @param callable $callback
     *
     * @return CallableNormalizer
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
