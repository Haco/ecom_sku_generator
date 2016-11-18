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
 * A part available to configuration
 */
class Part extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * @var int
     */
    protected $sorting = 0;

    /**
     * The part title
     *
     * @var string
     * @validate NotEmpty
     */
    protected $title = '';

    /**
     * A part image
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    protected $image = null;

    /**
     * Give user a hint
     *
     * @var string
     */
    protected $hint = '';

    /**
     * Incompatible Note
     *
     * @var string
     */
    protected $incompatibleNote = '';

    /**
     * Part pricing
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomConfigCodeGenerator\Domain\Model\Price>
     * @cascade remove
     */
    public $pricing = null;

    /**
     * Part min order quantity
     *
     * @var integer
     */
    protected $minOrderQuantity = 0;

    /**
     * @var string
     */
    public $currencyPricing = '';

    /**
     * @var float
     */
    public $noCurrencyPricing = 0.0;

    /**
     * @var string
     */
    protected $differencePricing = '';

    /**
     * @var \S3b0\EcomConfigCodeGenerator\Domain\Model\Currency
     */
    protected $currency;

    /**
     * @var \S3b0\EcomSkuGenerator\Domain\Model\PartGroup
     */
    protected $partGroup = null;

    /**
     * @var bool
     */
    protected $active = false;

    /**
     * @var bool
     */
    protected $compatibleToSelection = false;

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
        $this->pricing = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * @return int $sorting
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the image
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Sets the image
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
     * @return void
     */
    public function setImage(\TYPO3\CMS\Extbase\Domain\Model\FileReference $image)
    {
        $this->image = $image;
    }

    /**
     * Returns the hint
     *
     * @return string $hint
     */
    public function getHint()
    {
        return $this->hint;
    }

    /**
     * Sets the hint
     *
     * @param string $hint
     * @return void
     */
    public function setHint($hint)
    {
        $this->hint = $hint;
    }

    /**
     * Returns the incompatibleNote
     *
     * @return string $incompatibleNote
     */
    public function getIncompatibleNote()
    {
        return $this->incompatibleNote;
    }

    /**
     * Sets the incompatibleNote
     *
     * @param string $incompatibleNote
     * @return void
     */
    public function setIncompatibleNote($incompatibleNote)
    {
        $this->incompatibleNote = $incompatibleNote;
    }

    /**
     * Returns the minOrderQuantity
     *
     * @return integer $minOrderQuantity
     */
    public function getMinOrderQuantity()
    {
        return $this->minOrderQuantity;
    }

    /**
     * Sets the minOrderQuantity
     *
     * @param integer $minOrderQuantity
     * @return void
     */
    public function setMinOrderQuantity($minOrderQuantity)
    {
        $this->minOrderQuantity = $minOrderQuantity;
    }

    /**
     * Adds a Price
     *
     * @param \S3b0\EcomConfigCodeGenerator\Domain\Model\Price $pricing
     * @return void
     */
    public function addPricing(\S3b0\EcomConfigCodeGenerator\Domain\Model\Price $pricing)
    {
        $this->pricing->attach($pricing);
    }

    /**
     * Removes a Price
     *
     * @param \S3b0\EcomConfigCodeGenerator\Domain\Model\Price $pricingToRemove The Price to be removed
     * @return void
     */
    public function removePricing(\S3b0\EcomConfigCodeGenerator\Domain\Model\Price $pricingToRemove)
    {
        $this->pricing->detach($pricingToRemove);
    }

    /**
     * Returns the pricing
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomConfigCodeGenerator\Domain\Model\Price> $pricing
     */
    public function getPricing()
    {
        return $this->pricing;
    }

    /**
     * Sets the pricing
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomConfigCodeGenerator\Domain\Model\Price> $pricing
     * @return void
     */
    public function setPricing(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $pricing)
    {
        $this->pricing = $pricing;
    }

    /**
     * Returns the currencyPricing
     *
     * @return string $currencyPricing
     */
    public function getCurrencyPricing()
    {
        return $this->currencyPricing;
    }

    /**
     * Sets the currencyPricing
     *
     * @param string $currencyPricing
     */
    public function setCurrencyPricing($currencyPricing)
    {
        $this->currencyPricing = $currencyPricing;
    }

    /**
     * Returns the noCurrencyPricing
     *
     * @return float $noCurrencyPricing
     */
    public function getNoCurrencyPricing()
    {
        return $this->noCurrencyPricing;
    }

    /**
     * Sets the noCurrencyPricing
     *
     * @param float $noCurrencyPricing
     */
    public function setNoCurrencyPricing($noCurrencyPricing)
    {
        $this->noCurrencyPricing = $noCurrencyPricing;
    }

    /**
     * Returns the differencePricing
     *
     * @return string $differencePricing
     */
    public function getDifferencePricing()
    {
        if ($this->partGroup->isMultipleSelectable()) {
            $value = $this->noCurrencyPricing * ($this->active ? -1 : 1);
        } else {
            if ($this->active) {
                $value = $this->noCurrencyPricing * -1;
            } else {
                $value = $this->noCurrencyPricing - $this->partGroup->getPricingNumeric();
            }
        }
        $value = \S3b0\EcomSkuGenerator\Utility\PriceHandler::getPriceInCurrency($value, $this->currency, true);

        return $value;
    }

    /**
     * Sets the differencePricing
     *
     * @param string $differencePricing
     */
    public function setDifferencePricing($differencePricing)
    {
        $this->differencePricing = $differencePricing;
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
     * @param array $settings
     */
    public function setCurrency(\S3b0\EcomConfigCodeGenerator\Domain\Model\Currency $currency, array $settings = [])
    {
        $this->currency = $currency;
        \S3b0\EcomSkuGenerator\Utility\PriceHandler::setPriceInCurrency($this, $currency, $settings);
    }

    /**
     * @return bool
     */
    public function isLiableToPayCosts()
    {
        return $this->pricing instanceof \TYPO3\CMS\Extbase\Persistence\ObjectStorage && $this->pricing->count();
    }

    /**
     * Returns the part group
     *
     * @return \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $partGroup
     */
    public function getPartGroup()
    {
        return $this->partGroup;
    }

    /**
     * Sets the part group
     *
     * @param \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $partGroup
     * @return void
     */
    public function setPartGroup(\S3b0\EcomSkuGenerator\Domain\Model\PartGroup $partGroup)
    {
        $this->partGroup = $partGroup;
    }

    /**
     * @return bool $active
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return boolean
     */
    public function isCompatibleToSelection()
    {
        return $this->compatibleToSelection;
    }

    /**
     * @return boolean
     */
    public function isIncompatibleToSelection()
    {
        return !$this->compatibleToSelection;
    }

    /**
     * @param boolean $compatibleToSelection
     */
    public function setCompatibleToSelection($compatibleToSelection)
    {
        $this->compatibleToSelection = $compatibleToSelection;
    }

    /**
     * @return boolean
     */
    public function isInConflictWithSelectedParts()
    {
        return !$this->compatibleToSelection;
    }

}