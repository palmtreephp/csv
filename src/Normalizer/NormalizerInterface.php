<?php

namespace Palmtree\Csv\Normalizer;

interface NormalizerInterface
{
    /**
     * @param string $value
     *
     * @return mixed
     */
    public function normalize($value);
}
