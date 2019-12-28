<?php

namespace Palmtree\Csv\Normalizer;

interface NormalizerInterface
{
    /**
     * @return mixed
     */
    public function normalize(string $value);
}
