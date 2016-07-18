<?php
namespace S3b0\EcomSkuGenerator\Utility;

/**
 * Class PriceHandler
 * @package S3b0\EcomSkuGenerator\Utility
 */
class PriceHandler
{

    /**
     * @param float $value
     * @param \S3b0\EcomConfigCodeGenerator\Domain\Model\Currency|NULL $currency
     * @param bool $signed
     * @return null|string
     */
    public static function getPriceInCurrency(
        $value,
        \S3b0\EcomConfigCodeGenerator\Domain\Model\Currency $currency = null,
        $signed = false
    ) {
        if ($currency instanceof \S3b0\EcomConfigCodeGenerator\Domain\Model\Currency) {
            $dec_point = $currency->isNumberSeparatorInUSFormat() ? '.' : ',';
            $thousands_sep = $currency->isNumberSeparatorInUSFormat() ? ',' : '.';
            $value = ($signed && $value > 0 ? '+' : '') . number_format($value, 2, $dec_point, $thousands_sep);
            $whitespace = $currency->isWhitespaceBetweenCurrencyAndValue() ? ' ' : '';
            if ($currency->isSymbolPrepended()) {
                $value = "{$currency->getSymbol()}{$whitespace}{$value}";
            } else {
                $value = "{$value}{$whitespace}{$currency->getSymbol()}";
            }
        }

        return $value;
    }

    /**
     * @param \TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject $model
     * @param \S3b0\EcomConfigCodeGenerator\Domain\Model\Currency|NULL $currency
     * @param array $settings
     * @param array $setNumericFields
     * @param string $setStringField
     * @param string $pricingField
     */
    public static function setPriceInCurrency(
        \TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject $model,
        \S3b0\EcomConfigCodeGenerator\Domain\Model\Currency $currency = null,
        array $settings,
        $setNumericFields = ['noCurrencyPricing'],
        $setStringField = 'currencyPricing',
        $pricingField = 'pricing'
    ) {
        if ($currency instanceof \S3b0\EcomConfigCodeGenerator\Domain\Model\Currency) {
            $value = '0.00';
            $priceFound = false;
            $numberValue = 0;
            /** @var \S3b0\EcomConfigCodeGenerator\Domain\Model\Price $default */
            $default = null;
            $dec_point = $currency->isNumberSeparatorInUSFormat() ? '.' : ',';
            $thousands_sep = $currency->isNumberSeparatorInUSFormat() ? ',' : '.';
            if ($model->{$pricingField}->count()) {
                /**
                 * Step 1: Get price in current currency if set >>
                 * @var \S3b0\EcomConfigCodeGenerator\Domain\Model\Price $pricing
                 */
                foreach ($model->{$pricingField} as $pricing) {
                    $compareCurrency = $pricing->getCurrency() instanceof \TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy ? $pricing->getCurrency()->_loadRealInstance() : $pricing->getCurrency();
                    if ($compareCurrency->getUid() == $settings['defaultCurrency']) {
                        $default = $pricing;
                    }
                    if ($compareCurrency === $currency) {
                        $priceFound = true;
                        $numberValue = $pricing->getValue();
                        $value = number_format($pricing->getValue(), 2, $dec_point, $thousands_sep);
                    }
                }
                /**
                 * Step 2: Get price by calculation using default price and exchange rate >>
                 */
                if (!$priceFound && $default instanceof \S3b0\EcomConfigCodeGenerator\Domain\Model\Price && $default->getCurrency() !== $currency) {
                    $calculatedValue = $default->getValue() * $currency->getExchange();
                    if ($calculatedValue) {
                        $numberValue = $calculatedValue;
                        $value = number_format($calculatedValue, 2, $dec_point, $thousands_sep);
                    }
                }
            }
            /**
             * Step 3: If still no price is available, set 'em to zero
             */
            $whitespace = $currency->isWhitespaceBetweenCurrencyAndValue() ? ' ' : '';
            if ($currency->isSymbolPrepended()) {
                $stringValue = "{$currency->getSymbol()}{$whitespace}{$value}";
            } else {
                $stringValue = "{$value}{$whitespace}{$currency->getSymbol()}";
            }
        } else {
            $numberValue = 0;
            $stringValue = '0';
        }
        /** Set numeric values */
        foreach ($setNumericFields as $field) {
            if ($model->_hasProperty($field)) {
                $model->_setProperty($field, $numberValue);
            }
        }
        /** Set string value */
        $model->{$setStringField} = $stringValue;
    }

}