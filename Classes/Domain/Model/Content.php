<?php
namespace S3b0\EcomSkuGenerator\Domain\Model;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Sebastian Iffland <Sebastian.Iffland@ecom-ex.com>, ecom instruments GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Extending tt_content table
 */
class Content extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * @var string
     */
    protected $bodytext = '';

    /**
     * @var string
     */
    protected $header = '';

    /**
     * Link content element to part groups
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\PartGroup>
     * @cascade remove
     * @lazy
     */
    protected $skuGeneratorPartGroups = null;

    /**
     * Link content element to configurations
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\Configuration>
     * @cascade remove
     * @lazy
     */
    protected $skuGeneratorConfigurations = null;

    /**
     * Link content element to configuration
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomConfigCodeGenerator\Domain\Model\Price>
     * @cascade remove
     * @lazy
     */
    protected $skuGeneratorPricing = null;

    /**
     * @var bool
     */
    protected $skuGeneratorPricingEnabled = false;

    /**
     * @var float
     */
    protected $basePrice = 0.0;

    /**
     * @var float
     */
    protected $configurationPrice = 0.0;

    /**
     * @var \S3b0\EcomConfigCodeGenerator\Domain\Model\Currency
     */
    protected $currency = null;

    /**
     * __construct
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->skuGeneratorPartGroups = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->skuGeneratorConfigurations = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->skuGeneratorPricing = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param string $header
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     * @return string
     */
    public function getBodytext()
    {
        return $this->bodytext;
    }

    /**
     * @param string $bodytext
     */
    public function setBodytext($bodytext)
    {
        $this->bodytext = $bodytext;
    }

    /**
     * Adds a PartGroup
     *
     * @param \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $skuGeneratorPartGroup
     * @return void
     */
    public function addSkuGeneratorPartGroup(\S3b0\EcomSkuGenerator\Domain\Model\PartGroup $skuGeneratorPartGroup)
    {
        $this->skuGeneratorPartGroups->attach($skuGeneratorPartGroup);
    }

    /**
     * Removes a PartGroup
     *
     * @param \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $skuGeneratorPartGroupToRemove The PartGroup to be removed
     * @return void
     */
    public function removeSkuGeneratorPartGroup(\S3b0\EcomSkuGenerator\Domain\Model\PartGroup $skuGeneratorPartGroupToRemove) {
        $this->skuGeneratorPartGroups->detach($skuGeneratorPartGroupToRemove);
    }

    /**
     * Returns the skuGeneratorPartGroups
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\PartGroup> $skuGeneratorPartGroups
     */
    public function getSkuGeneratorPartGroups()
    {
        return $this->skuGeneratorPartGroups;
    }

    /**
     * Sets the skuGeneratorPartGroups
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\PartGroup> $skuGeneratorPartGroups
     * @return void
     */
    public function setSkuGeneratorPartGroups(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $skuGeneratorPartGroups)
    {
        $this->skuGeneratorPartGroups = $skuGeneratorPartGroups;
    }

    /**
     * Adds a Configuration
     *
     * @param \S3b0\EcomSkuGenerator\Domain\Model\Configuration $skuGeneratorConfiguration
     * @return void
     */
    public function addSkuGeneratorConfiguration(\S3b0\EcomSkuGenerator\Domain\Model\Configuration $skuGeneratorConfiguration) {
        $this->skuGeneratorConfigurations->attach($skuGeneratorConfiguration);
    }

    /**
     * Removes a Configuration
     *
     * @param \S3b0\EcomSkuGenerator\Domain\Model\Configuration $skuGeneratorConfigurationToRemove The Configuration to
     *                                                                                             be removed
     * @return void
     */
    public function removeSkuGeneratorConfiguration(\S3b0\EcomSkuGenerator\Domain\Model\Configuration $skuGeneratorConfigurationToRemove) {
        $this->skuGeneratorConfigurations->detach($skuGeneratorConfigurationToRemove);
    }

    /**
     * Returns the skuGeneratorConfigurations
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\Configuration> $skuGeneratorConfigurations
     */
    public function getSkuGeneratorConfigurations()
    {
        return $this->skuGeneratorConfigurations;
    }

    /**
     * Sets the skuGeneratorConfigurations
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\Configuration> $skuGeneratorConfigurations
     * @return void
     */
    public function setSkuGeneratorConfigurations(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $skuGeneratorConfigurations) {
        $this->skuGeneratorConfigurations = $skuGeneratorConfigurations;
    }

    /**
     * Adds a Price
     *
     * @param \S3b0\EcomConfigCodeGenerator\Domain\Model\Price $skuGeneratorPricing
     * @return void
     */
    public function addSkuGeneratorPricing(\S3b0\EcomConfigCodeGenerator\Domain\Model\Price $skuGeneratorPricing)
    {
        $this->skuGeneratorPricing->attach($skuGeneratorPricing);
    }

    /**
     * Removes a Price
     *
     * @param \S3b0\EcomConfigCodeGenerator\Domain\Model\Price $skuGeneratorPricingToRemove The Price to be removed
     * @return void
     */
    public function removeSkuGeneratorPricing(\S3b0\EcomConfigCodeGenerator\Domain\Model\Price $skuGeneratorPricingToRemove) {
        $this->skuGeneratorPricing->detach($skuGeneratorPricingToRemove);
    }

    /**
     * Returns the skuGeneratorPricing
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomConfigCodeGenerator\Domain\Model\Price> $skuGeneratorPricing
     */
    public function getSkuGeneratorPricing()
    {
        return $this->skuGeneratorPricing;
    }

    /**
     * Sets the skuGeneratorPricing
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomConfigCodeGenerator\Domain\Model\Price> $skuGeneratorPricing
     * @return void
     */
    public function setSkuGeneratorPricing(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $skuGeneratorPricing)
    {
        $this->skuGeneratorPricing = $skuGeneratorPricing;
    }

    /**
     * @return bool
     */
    public function isPricingEnabled()
    {
        return $this->skuGeneratorPricingEnabled;
    }

    /**
     * @return float
     */
    public function getConfigurationPrice()
    {
        return $this->configurationPrice;
    }

    /**
     * @return float
     */
    public function getBasePrice()
    {
        return $this->basePrice;
    }

    /**
     * @return void
     */
    public function setBasePrice()
    {
        $price = 0.0;
        if ($this->getSkuGeneratorPricing() instanceof \TYPO3\CMS\Extbase\Persistence\ObjectStorage && $this->getSkuGeneratorPricing()->count()) {
            /** @var \S3b0\EcomConfigCodeGenerator\Domain\Model\Price $pricing */
            foreach ($this->skuGeneratorPricing as $pricing) {
                if ($pricing->getCurrency() === $this->currency) {
                    $price = $pricing->getValue();
                }
            }
        }

        $this->basePrice = $price;
        $this->setConfigurationPrice($price);
    }

    /**
     * @return string
     */
    public function getBasePriceFormatted()
    {
        return $this->basePrice ? \S3b0\EcomSkuGenerator\Utility\PriceHandler::getPriceInCurrency($this->basePrice, $this->getCurrency()) : '-';
    }

    /**
     * @param float $configurationPrice
     */
    public function setConfigurationPrice($configurationPrice)
    {
        $this->configurationPrice = $configurationPrice;
    }

    /**
     * @param float $summand
     */
    public function sumUpConfigurationPrice($summand)
    {
        $this->configurationPrice += $summand;
    }

    /**
     * @return string
     */
    public function getConfigurationPriceFormatted()
    {
        return $this->configurationPrice ? \S3b0\EcomSkuGenerator\Utility\PriceHandler::getPriceInCurrency($this->configurationPrice, $this->getCurrency()) : '-';
    }

    /**
     * @return \S3b0\EcomConfigCodeGenerator\Domain\Model\Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param \S3b0\EcomConfigCodeGenerator\Domain\Model\Currency $currency
     */
    public function setCurrency(\S3b0\EcomConfigCodeGenerator\Domain\Model\Currency $currency)
    {
        $this->currency = $currency;
        $this->setBasePrice();
    }

}