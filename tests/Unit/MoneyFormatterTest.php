<?php

namespace SanderVanHooft\Invoicable\Unit;

use SanderVanHooft\Invoicable\AbstractTestCase;
use SanderVanHooft\Invoicable\MoneyFormatter;

class MoneyFormatterTest extends AbstractTestCase
{
    public function setUp()
    {
        $this->formatter = new MoneyFormatter();
    }

    /** @test */
    public function canHandleNegativeValues()
    {
        $formatted = $this->formatter->format(-123456);
        $this->assertEquals('€ -1.234,56', $formatted);
    }

    /** @test */
    public function canFormatMoney()
    {
        $formatter = new MoneyFormatter();
        $formatted = $formatter->format(123456);
        $this->assertEquals('€ 1.234,56', $formatted);
    }

    /** @test */
    public function changingTheCurrencyChangesTheFormatting()
    {
        $formatter = new MoneyFormatter();
        $formatter->setCurrency('USD');
        $formatted = $formatter->format(123456);
        $this->assertEquals('US$ 1.234,56', $formatted);
    }

    /** @test */
    public function changingTheLocaleChangesTheFormatting()
    {
        $formatter = new MoneyFormatter();
        $formatter->setLocale('en_US');
        $formatted = $formatter->format(123456);
        $this->assertEquals('€1,234.56', $formatted);
    }

    /** @test */
    public function changingTheCurrencyAndLocaleChangesTheFormatting()
    {
        $formatter = new MoneyFormatter();
        $formatter->setCurrency('USD');
        $formatter->setLocale('en_US');
        $formatted = $formatter->format(123456);
        $this->assertEquals('$1,234.56', $formatted);
    }
}