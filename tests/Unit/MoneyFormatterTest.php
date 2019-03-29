<?php

namespace SanderVanHooft\Invoicable\Unit;

use Money\Currency;
use Money\Money;
use SanderVanHooft\Invoicable\AbstractTestCase;
use SanderVanHooft\Invoicable\MoneyFormatter;

class MoneyFormatterTest extends AbstractTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->formatter = new MoneyFormatter();
    }

    /** @test */
    public function canHandleNegativeValues()
    {
        $this->assertTrue(in_array($this->formatter->format($this->asMoney(-123456)), [
            '€ -1.234,56',
            '€ 1.234,56-',
        ]));
    }

    /** @test */
    public function canFormatMoney()
    {
        $this->assertEquals('€ 1.234,56', $this->formatter->format($this->asMoney(123456)));
    }

    /** @test */
    public function changingTheCurrencyChangesTheFormatting()
    {
        $this->assertEquals('US$ 1.234,56', $this->formatter->format($this->asMoney(123456, 'USD')));
    }

    /** @test */
    public function changingTheLocaleChangesTheFormatting()
    {
        $this->formatter->setLocale('en_US');
        $this->assertEquals('€1,234.56', $this->formatter->format($this->asMoney(123456)));
    }

    /** @test */
    public function changingTheCurrencyAndLocaleChangesTheFormatting()
    {
        $this->formatter->setLocale('en_US');
        $this->assertEquals('$1,234.56', $this->formatter->format($this->asMoney(123456, 'USD')));
    }

    /**
     * @param int $amount
     * @param string $currency
     * @return \Money\Money
     */
    protected function asMoney(int $amount, string $currency = 'EUR')
    {
        return new Money($amount, new Currency($currency));
    }
}
