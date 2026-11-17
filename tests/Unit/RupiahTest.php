<?php

namespace Tests\Unit;

use App\Support\Rupiah;
use PHPUnit\Framework\TestCase;

class RupiahTest extends TestCase
{
    public function test_format_memakai_titik_sebagai_pemisah_ribuan(): void
    {
        $this->assertSame('Rp 21.000', Rupiah::format(21000));
        $this->assertSame('Rp 1.658', Rupiah::format(1658));
        $this->assertSame('Rp 0', Rupiah::format(0));
        $this->assertSame('−Rp 500', Rupiah::format(-500));
        $this->assertSame('–', Rupiah::format(null));
    }

    public function test_parse_membaca_isian_bertitik(): void
    {
        $this->assertSame(21000, Rupiah::parse('21.000'));
        $this->assertSame(14542, Rupiah::parse('Rp 14.542'));
        $this->assertSame(600, Rupiah::parse(600));
        $this->assertSame(0, Rupiah::parse(''));
        $this->assertSame(0, Rupiah::parse(null));
    }
}
