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
 * Configuration record
 */
class Configuration extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * Work title
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $title = '';

	/**
	 * Corresponding SKU
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $sku = '';

	/**
	 * Selectable parts
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\Part>
	 */
	protected $parts = NULL;

	/**
	 * Configuration base pricing
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomConfigCodeGenerator\Domain\Model\Price>
	 * @cascade remove
	 */
	public $pricing = NULL;

	/**
	 * @var string
	 */
	public $currencyPricing = '';

	/**
	 * @var float
	 */
	public $noCurrencyPricing = 0.0;

	/**
	 * @var \S3b0\EcomConfigCodeGenerator\Domain\Model\Currency
	 */
	protected $currency;

	/**
	 * __construct
	 */
	public function __construct() {
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
	protected function initStorageObjects() {
		$this->parts = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->pricing = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * Returns the title
	 *
	 * @return string $title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Sets the title
	 *
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Returns the sku
	 *
	 * @return string $sku
	 */
	public function getSku() {
		return $this->sku;
	}

	/**
	 * Sets the sku
	 *
	 * @param string $sku
	 * @return void
	 */
	public function setSku($sku) {
		$this->sku = $sku;
	}

	/**
	 * Adds a Part
	 *
	 * @param \S3b0\EcomSkuGenerator\Domain\Model\Part $part
	 * @return void
	 */
	public function addPart(\S3b0\EcomSkuGenerator\Domain\Model\Part $part) {
		$this->parts->attach($part);
	}

	/**
	 * Removes a Part
	 *
	 * @param \S3b0\EcomSkuGenerator\Domain\Model\Part $partToRemove The Part to be removed
	 * @return void
	 */
	public function removePart(\S3b0\EcomSkuGenerator\Domain\Model\Part $partToRemove) {
		$this->parts->detach($partToRemove);
	}

	/**
	 * Returns the parts
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\Part> $parts
	 */
	public function getParts() {
		return $this->parts;
	}

	/**
	 * Sets the parts
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\Part> $parts
	 * @return void
	 */
	public function setParts(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $parts) {
		$this->parts = $parts;
	}

	/**
	 * Returns parts by part group
	 *
	 * @param \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $partGroup
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\Part> $parts
	 */
	public function getPartsByPartGroup(\S3b0\EcomSkuGenerator\Domain\Model\PartGroup $partGroup) {
		$parts = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();

		/** @var \S3b0\EcomSkuGenerator\Domain\Model\Part $part */
		foreach ( $this->parts as $part ) {
			if ( $part->getPartGroup() === $partGroup )
				$parts->attach($part);
		}

		return $parts;
	}

	/**
	 * Adds a Price
	 *
	 * @param \S3b0\EcomConfigCodeGenerator\Domain\Model\Price $pricing
	 * @return void
	 */
	public function addPricing(\S3b0\EcomConfigCodeGenerator\Domain\Model\Price $pricing) {
		$this->pricing->attach($pricing);
	}

	/**
	 * Removes a Price
	 *
	 * @param \S3b0\EcomConfigCodeGenerator\Domain\Model\Price $pricingToRemove The Price to be removed
	 * @return void
	 */
	public function removePricing(\S3b0\EcomConfigCodeGenerator\Domain\Model\Price $pricingToRemove) {
		$this->pricing->detach($pricingToRemove);
	}

	/**
	 * Returns the pricing
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomConfigCodeGenerator\Domain\Model\Price> $pricing
	 */
	public function getPricing() {
		return $this->pricing;
	}

	/**
	 * Sets the pricing
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomConfigCodeGenerator\Domain\Model\Price> $pricing
	 * @return void
	 */
	public function setPricing(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $pricing) {
		$this->pricing = $pricing;
	}

	/**
	 * @return string $currencyPricing
	 */
	public function getCurrencyPricing() {
		return $this->noCurrencyPricing;
	}

	/**
	 * @param string $currencyPricing
	 */
	public function setCurrencyPricing($currencyPricing) {
		$this->currencyPricing = $currencyPricing;
	}

	/**
	 * @return float $noCurrencyPricing
	 */
	public function getNoCurrencyPricing() {
		return $this->noCurrencyPricing;
	}

	/**
	 * @param float $noCurrencyPricing
	 */
	public function setNoCurrencyPricing($noCurrencyPricing) {
		$this->noCurrencyPricing = $noCurrencyPricing;
	}

	/**
	 * @return \S3b0\EcomConfigCodeGenerator\Domain\Model\Currency
	 */
	public function getCurrency() {
		return $this->currency;
	}

	/**
	 * @param \S3b0\EcomConfigCodeGenerator\Domain\Model\Currency $currency
	 * @param array                                               $settings
	 */
	public function setCurrency(\S3b0\EcomConfigCodeGenerator\Domain\Model\Currency $currency, array $settings = [ ]) {
		\S3b0\EcomSkuGenerator\Utility\PriceHandler::setPriceInCurrency($this, $currency, $settings);
		$this->currency = $currency;
	}

	/**
	 * @return bool
	 */
	public function isLiableToPayCosts() {
		return $this->pricing instanceof \TYPO3\CMS\Extbase\Persistence\ObjectStorage && $this->pricing->count();
	}

}