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
 * Dependency notes, that appear when special parts have been chosen
 */
class DependentNote extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * The note itself
     *
     * @var string
     * @validate NotEmpty
     */
    protected $note = '';

    /**
     * Specifies whether to use logical OR or AND chaining for dependent parts
     *
     * @var boolean
     */
    protected $useLogicalAnd = false;

    /**
     * Parts the note display depends on
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\Part>
     */
    protected $dependentParts = null;

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
        $this->dependentParts = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Returns the note
     *
     * @return string $note
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Sets the note
     *
     * @param string $note
     * @return void
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * Returns the useLogicalAnd
     *
     * @return boolean $useLogicalAnd
     */
    public function getUseLogicalAnd()
    {
        return $this->useLogicalAnd;
    }

    /**
     * Sets the useLogicalAnd
     *
     * @param boolean $useLogicalAnd
     * @return void
     */
    public function setUseLogicalAnd($useLogicalAnd)
    {
        $this->useLogicalAnd = $useLogicalAnd;
    }

    /**
     * Returns the boolean state of useLogicalAnd
     *
     * @return boolean
     */
    public function isUseLogicalAnd()
    {
        return $this->useLogicalAnd;
    }

    /**
     * Adds a Part
     *
     * @param \S3b0\EcomSkuGenerator\Domain\Model\Part $dependentPart
     * @return void
     */
    public function addDependentPart(\S3b0\EcomSkuGenerator\Domain\Model\Part $dependentPart)
    {
        $this->dependentParts->attach($dependentPart);
    }

    /**
     * Removes a Part
     *
     * @param \S3b0\EcomSkuGenerator\Domain\Model\Part $dependentPartToRemove The Part to be removed
     * @return void
     */
    public function removeDependentPart(\S3b0\EcomSkuGenerator\Domain\Model\Part $dependentPartToRemove)
    {
        $this->dependentParts->detach($dependentPartToRemove);
    }

    /**
     * Returns the dependentParts
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\Part> $dependentParts
     */
    public function getDependentParts()
    {
        return $this->dependentParts;
    }

    /**
     * Sets the dependentParts
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\Part> $dependentParts
     * @return void
     */
    public function setDependentParts(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $dependentParts)
    {
        $this->dependentParts = $dependentParts;
    }

}