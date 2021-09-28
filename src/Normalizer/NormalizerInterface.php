<?php

declare(strict_types=1);

namespace Palmtree\Csv\Normalizer;

interface NormalizerInterface
{
    public function normalize(string $value): mixed;
}
