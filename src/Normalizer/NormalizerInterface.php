<?php

declare(strict_types=1);

namespace Palmtree\Csv\Normalizer;

interface NormalizerInterface
{
    /** @return mixed */
    public function normalize(string $value);
}
