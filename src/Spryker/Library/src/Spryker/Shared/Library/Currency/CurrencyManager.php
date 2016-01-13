<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Shared\Library\Currency;

use Spryker\Shared\Library\Currency\CurrencyConstants;
use Spryker\Shared\Kernel\Store;

/**
 * This class is the central math class for currency arithmetic operations
 */

class CurrencyManager
{

    const PRICE_PRECISION = 100;

    /**
     * @var CurrencyInterface
     */
    private static $currency;

    /**
     * @var self
     */
    private static $instance;

    private function __construct()
    {
    }

    /**
     * @return self
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param CurrencyInterface $currency
     *
     * @return void
     */
    public static function setDefaultCurrency(CurrencyInterface $currency)
    {
        self::$currency = $currency;
    }

    /**
     * @param string $isoCode
     *
     * @return void
     */
    public static function setDefaultCurrencyIso($isoCode)
    {
        self::$currency = self::loadCurrencyClass($isoCode);
    }

    /**
     * @return CurrencyInterface
     */
    public static function getDefaultCurrency()
    {
        if (!self::$currency) {
            self::$currency = self::loadCurrencyClass(Store::getInstance()->getCurrencyIsoCode());
        }

        return self::$currency;
    }

    /**
     * @param string $currencyIsoCode
     *
     * @return CurrencyInterface
     */
    protected static function loadCurrencyClass($currencyIsoCode)
    {
        $class = '\Spryker\\Shared\\Library\\Currency\\Config\\' . $currencyIsoCode;

        return new $class();
    }

    /**
     * This method should never return a number with a thousend separator, otherwise
     * the next call to number_format will leeds to an error
     *
     * @param int $centValue
     *
     * @return float
     */
    public function convertCentToDecimal($centValue)
    {
        return number_format($centValue / self::PRICE_PRECISION, 2, '.', '');
    }

    /**
     * @param float $decimalValue
     *
     * @return int
     */
    public function convertDecimalToCent($decimalValue)
    {
        return $decimalValue * self::PRICE_PRECISION;
    }

    /**
     * Ceil the current value
     * Solves precision lose problems, like in:
     * -((0.1+0.7)*10), ('34.200' + 0) * 100)
     * Specify expected decimalPlacesInUse to avoid false rounding
     *
     *
     * @param $value
     * @param int $decimalPlacesInUse
     *
     * @return float
     */
    public static function ceil($value, $decimalPlacesInUse = 2)
    {
        return ceil(round($value, $decimalPlacesInUse + 1));
    }

    /**
     * @param int|float $value
     * @param bool $includeSymbol
     *
     * @return int
     */
    public function format($value, $includeSymbol = true)
    {
        return self::formatCurrency($this->getDefaultCurrency(), $value, $includeSymbol);
    }

    /**
     * @param string $isoCode
     * @param int|float $value
     * @param bool $includeSymbol
     *
     * @return string
     */
    public function formatByIsoCode($isoCode, $value, $includeSymbol = true)
    {
        return self::formatCurrency($this->loadCurrencyClass($isoCode), $value, $includeSymbol);
    }

    /**
     * @param CurrencyInterface $currency
     * @param int|float $value
     * @param bool $includeSymbol
     *
     * @return string|null
     */
    protected function formatCurrency(CurrencyInterface $currency, $value, $includeSymbol = true)
    {
        if ($value === null) {
            return $value;
        }
        $value = $this->formatNumber($currency, $value);

        if ($includeSymbol === true) {
            $value = str_replace(
                [CurrencyConstants::PLACEHOLDER_VALUE, CurrencyConstants::PLACEHOLDER_SYMBOL],
                [$value, $currency->getSymbol()],
                $currency->getFormatPattern()
            );
        }

        return $value;
    }

    /**
     * @param CurrencyInterface $currency
     * @param int|float $value
     *
     * @return string
     */
    protected function formatNumber(CurrencyInterface $currency, $value)
    {
        return number_format($value, $currency->getDecimalDigits(), $currency->getDecimalSeparator(), $currency->getThousandsSeparator());
    }

}
