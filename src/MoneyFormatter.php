<?php

namespace SanderVanHooft\Invoicable;

use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;

class MoneyFormatter
{
    /**
     * The current locale.
     *
     * @var string
     */
    protected $locale;

    /**
     * MoneyFormatter constructor.
     *
     * @param string $locale
     */
    public function __construct(string $locale = 'nl_NL')
    {
        $this->locale = $locale;
    }

    /**
     * Format the amount into a string.
     *
     * @param \Money\Money $amount The amount
     * @return String The current locale
     */
    public function format(Money $amount): string
    {
        $numberFormatter = new \NumberFormatter($this->getLocale(), \NumberFormatter::CURRENCY);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, new ISOCurrencies);

        return $moneyFormatter->format($amount);
    }

    /**
     * Gets the current locale
     * @return String The current locale
     */
    public function getLocale(): string
    {
        return (string) $this->locale;
    }

    /**
     * Sets the current locale
     *
     * @param string $locale The locale (i.e. 'nl_NL')
     * @return $this
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }
}
