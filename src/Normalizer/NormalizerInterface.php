<?php

namespace Palmtree\Csv\Normalizer;

interface NormalizerInterface
{
    public function normalize(string $value);
}
