<?php

declare(strict_types=1);

namespace Palmtree\Csv\Test\Normalizer;

use Palmtree\Csv\Normalizer\HtmlNormalizer;
use PHPUnit\Framework\TestCase;

class HtmlNormalizerTest extends TestCase
{
    public function testHtmlNormalizerEncodesHtmlByDefault(): void
    {
        $normalizer = new HtmlNormalizer();

        $result = $normalizer->normalize('<script>alert("xss")</script>');

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('&lt;', $result);
    }

    public function testHtmlNormalizerEncodesSpecialCharacters(): void
    {
        $normalizer = new HtmlNormalizer();

        $result = $normalizer->normalize('A & B');

        $this->assertStringContainsString('&amp;', $result);
    }

    public function testHtmlNormalizerWithEncodeDisabled(): void
    {
        $normalizer = new HtmlNormalizer();
        $normalizer->encode(false);

        $encoded = htmlentities('<p>Test</p>', \ENT_QUOTES);
        $result = $normalizer->normalize($encoded);

        $this->assertSame('<p>Test</p>', $result);
    }

    public function testHtmlNormalizerEncodeReturnsInstance(): void
    {
        $normalizer = new HtmlNormalizer();

        $result = $normalizer->encode(false);

        $this->assertSame($normalizer, $result);
    }

    public function testHtmlNormalizerFlagsReturnsInstance(): void
    {
        $normalizer = new HtmlNormalizer();

        $result = $normalizer->flags(\ENT_COMPAT);

        $this->assertSame($normalizer, $result);
    }

    public function testHtmlNormalizerWithDifferentFlags(): void
    {
        $input = 'Double "quotes" and single \'quotes\'';

        $quotesNormalizer = new HtmlNormalizer();
        $quotesNormalizer->flags(\ENT_QUOTES);
        $quotesResult = $quotesNormalizer->normalize($input);

        $compatNormalizer = new HtmlNormalizer();
        $compatNormalizer->flags(\ENT_COMPAT);
        $compatResult = $compatNormalizer->normalize($input);

        // ENT_QUOTES should encode both double and single quotes
        $this->assertStringContainsString('&quot;', $quotesResult);
        $this->assertStringContainsString('&#039;', $quotesResult);

        // ENT_COMPAT only encodes double quotes
        $this->assertStringContainsString('&quot;', $compatResult);
    }

    public function testHtmlNormalizerEncodeAndDecode(): void
    {
        $normalizer = new HtmlNormalizer();
        $normalizer->encode(true);

        $encoded = $normalizer->normalize('<p>Paragraph</p>');

        $normalizer->encode(false);
        $decoded = $normalizer->normalize($encoded);

        $this->assertSame('<p>Paragraph</p>', $decoded);
    }
}
