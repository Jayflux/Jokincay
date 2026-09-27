<?php

namespace Tests\Unit;

use App\Services\WhatsAppNormalizer;
use PHPUnit\Framework\TestCase;

class WhatsAppNormalizerTest extends TestCase
{
    public function test_normalizes_leading_zero(): void
    {
        $this->assertEquals('6281234567890', WhatsAppNormalizer::normalize('081234567890'));
    }

    public function test_normalizes_plus_sign(): void
    {
        $this->assertEquals('6281234567890', WhatsAppNormalizer::normalize('+6281234567890'));
    }

    public function test_normalizes_with_hyphens_and_spaces(): void
    {
        $this->assertEquals('6281234567890', WhatsAppNormalizer::normalize('+62 812-3456-7890'));
    }

    public function test_normalizes_leading_eight(): void
    {
        $this->assertEquals('6281234567890', WhatsAppNormalizer::normalize('81234567890'));
    }

    public function test_handles_empty(): void
    {
        $this->assertEquals('', WhatsAppNormalizer::normalize(null));
        $this->assertEquals('', WhatsAppNormalizer::normalize(''));
    }
}
