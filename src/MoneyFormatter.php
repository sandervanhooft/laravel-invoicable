<?php

namespace SanderVanHooft\Invoicable;

class MoneyFormatter
{

    /**
     * The current currency.
     *
     * @var string
     */
    protected $currency;

    /**
     * The current locale.
     *
     * @var string
     */
    protected $locale;

    public function __construct($currency = 'EUR', $locale = 'nl_NL')
    {
        $this->currency = $currency;
        $this->locale = $locale;
    }

    /**
     * Gets the amount formatted according the currency and locale
     * @param $amount The amount in cents(!)
     * @return String The current locale
     */
    public function format($amount)
    {
        $formatter = new \NumberFormatter($this->locale, \NumberFormatter::CURRENCY);
        return (string) $formatter->formatCurrency($amount / 100, $this->currency);
    }

    /**
     * Gets the current locale
     * @return String The current locale
     */
    public function getLocale() : string
    {
        return (string) $this->locale;
    }

    /**
     * Sets the current locale
     * @param $locale The locale (i.e. 'nl_NL')
     * @return Void
     */
    public function setLocale(string $locale)
    {
        $this->locale = $locale;
    }

    /**
     * Gets the current currency
     * @return String The current currency
     */
    public function getCurrency()
    {
        return (string) $this->currency;
    }

     /**
     * Sets the current currency
     * @param $currency The currency (i.e. 'EUR')
     * @return Void
     */
    public function setCurrency(string $currency)
    {
        $this->currency = $currency;
    }
}
