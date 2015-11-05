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
 * Log model
 */
class Log extends \S3b0\EcomConfigCodeGenerator\Domain\Model\Log {

	/**
	 * Configured parts
	 *
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\Part>
	 */
	protected $configuredPartsSku = NULL;

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
		$this->configuredParts = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		$this->configuredPartsSku = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
	}

	/**
	 * Adds a configured Part
	 *
	 * @param \S3b0\EcomSkuGenerator\Domain\Model\Part $configuredPartSku
	 * @return void
	 */
	public function addConfiguredPartSku(\S3b0\EcomSkuGenerator\Domain\Model\Part $configuredPartSku) {
		$this->configuredPartsSku->attach($configuredPartSku);
	}

	/**
	 * Removes a configured Part
	 *
	 * @param \S3b0\EcomSkuGenerator\Domain\Model\Part $configuredPartSkuToRemove The Modal to be removed
	 * @return void
	 */
	public function removeConfiguredPartSku(\S3b0\EcomSkuGenerator\Domain\Model\Part $configuredPartSkuToRemove) {
		$this->configuredPartsSku->detach($configuredPartSkuToRemove);
	}

	/**
	 * Returns the configuredPartsSku
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\Part> $configuredPartsSku
	 */
	public function getConfiguredPartsSku() {
		return $this->configuredPartsSku;
	}

	/**
	 * Sets the configuredPartsSku
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\Part> $configuredPartsSku
	 * @return \S3b0\EcomSkuGenerator\Domain\Model\Log Allow chaining of methods
	 */
	public function setConfiguredPartsSku(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $configuredPartsSku = NULL) {
		$this->configuredPartsSku = $configuredPartsSku;
		return $this;
	}

}