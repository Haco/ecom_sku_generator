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
use S3b0\EcomSkuGenerator\Setup;

/**
 * Group of parts available for configuration
 */
class PartGroup extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * @var int
     */
    protected $sorting = 0;

    /**
     * The partgroup title
     *
     * @var string
     * @validate NotEmpty
     */
    protected $title = '';

    /**
     * Icon file, if any
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    protected $icon = null;

    /**
     * User prompt plus hints, if any
     *
     * @var string
     */
    protected $prompt = '';

    /**
     * Wrapper <div class="alert alert-xxx"> (default Bootstrap classes)
     *
     * @var int
     */
    protected $promptWrap = 0;

    /**
     * Notes depending on divers parts selected
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\DependentNote>
     * @cascade remove
     * @lazy
     */
    protected $dependentNotes = null;

    /**
     * Global settings, i.e. visibility options, pricing options, multiple select
     * availability ...
     *
     * @var int
     */
    protected $settings = 0;

    /**
     * Parts belonging to the group
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\Part>
     * @cascade remove
     * @lazy
     */
    protected $parts = null;

    /**
     * @var string
     */
    protected $pricing = '';

    /**
     * @var float
     */
    protected $pricingNumeric = 0.0;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\Part>
     */
    protected $activeParts = null;

    /**
     * @var \ArrayObject
     */
    protected $dependentNotesFluidParsedMessages;

    /**
     * @var bool
     */
    protected $active = false;

    /**
     * @var bool
     */
    protected $current = false;

    /**
     * @var \S3b0\EcomSkuGenerator\Domain\Model\PartGroup
     */
    protected $next = null;

    /**
     * @var int
     */
    protected $stepIndicator = 0;

    /**
     * @var bool
     */
    protected $last = false;

    /**
     * __construct
     */
    public function __construct()
    {
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
        $this->parts = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->activeParts = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->dependentNotes = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->dependentNotesFluidParsedMessages = new \ArrayObject();
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
     * Returns the icon
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference $icon
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Sets the icon
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $icon
     * @return void
     */
    public function setIcon(\TYPO3\CMS\Extbase\Domain\Model\FileReference $icon)
    {
        $this->icon = $icon;
    }

    /**
     * Returns the prompt
     *
     * @return string $prompt
     */
    public function getPrompt()
    {
        switch ($this->promptWrap) {
            case 1:
                return "<div class=\"alert alert-success\">{$this->prompt}</div>";
                break;
            case 2:
                return "<div class=\"alert alert-info\">{$this->prompt}</div>";
                break;
            case 3:
                return "<div class=\"alert alert-warning\">{$this->prompt}</div>";
                break;
            case 4:
                return "<div class=\"alert alert-danger\">{$this->prompt}</div>";
                break;
            default:
                return $this->prompt;
        }
    }

    /**
     * Sets the prompt
     *
     * @param string $prompt
     * @return void
     */
    public function setPrompt($prompt)
    {
        $this->prompt = $prompt;
    }

    /**
     * Returns the promptWrap
     *
     * @return int $promptWrap
     */
    public function getPromptWrap()
    {
        return $this->promptWrap;
    }

    /**
     * Sets the promptWrap
     *
     * @param int $promptWrap
     * @return void
     */
    public function setPromptWrap($promptWrap)
    {
        $this->promptWrap = $promptWrap;
    }

    /**
     * Returns the settings
     *
     * @return int $settings
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Sets the settings
     *
     * @param int $settings
     * @return void
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
    }

    /**
     * Adds a Part
     *
     * @param \S3b0\EcomSkuGenerator\Domain\Model\Part $part
     * @return void
     */
    public function addPart(\S3b0\EcomSkuGenerator\Domain\Model\Part $part)
    {
        $this->parts->attach($part);
    }

    /**
     * Removes a Part
     *
     * @param \S3b0\EcomSkuGenerator\Domain\Model\Part $partToRemove The Part to be removed
     * @return void
     */
    public function removePart(\S3b0\EcomSkuGenerator\Domain\Model\Part $partToRemove)
    {
        $this->parts->detach($partToRemove);
    }

    /**
     * Returns the parts
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\Part> $parts
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * Sets the parts
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\Part> $parts
     * @return void
     */
    public function setParts(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $parts)
    {
        $this->parts = $parts;
    }

    /**
     * Adds a DependentNote
     *
     * @param \S3b0\EcomSkuGenerator\Domain\Model\DependentNote $dependentNote
     * @return void
     */
    public function addDependentNote(\S3b0\EcomSkuGenerator\Domain\Model\DependentNote $dependentNote)
    {
        $this->dependentNotes->attach($dependentNote);
    }

    /**
     * Removes a DependentNote
     *
     * @param \S3b0\EcomSkuGenerator\Domain\Model\DependentNote $dependentNoteToRemove The DependentNote to be removed
     *
     * @return void
     */
    public function removeDependentNote(\S3b0\EcomSkuGenerator\Domain\Model\DependentNote $dependentNoteToRemove)
    {
        $this->dependentNotes->detach($dependentNoteToRemove);
    }

    /**
     * Returns the dependentNotes
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\DependentNote> $dependentNotes
     */
    public function getDependentNotes()
    {
        return $this->dependentNotes;
    }

    /**
     * Sets the dependentNotes
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\DependentNote> $dependentNotes
     * @return void
     */
    public function setDependentNotes(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $dependentNotes)
    {
        $this->dependentNotes = $dependentNotes;
    }

    /**
     * Adds a dependentNotesFluidParsedMessages
     *
     * @param string $dependentNotesFluidParsedMessages
     * @return void
     */
    public function addDependentNotesFluidParsedMessage($dependentNotesFluidParsedMessages)
    {
        if (!$this->dependentNotesFluidParsedMessages instanceof \ArrayObject) {
            $this->dependentNotesFluidParsedMessages = new \ArrayObject();
        }
        $this->dependentNotesFluidParsedMessages->append($dependentNotesFluidParsedMessages);
    }

    /**
     * Returns the dependentNotesFluidParsedMessages
     *
     * @return string
     */
    public function getDependentNotesFluidParsedMessages()
    {
        $dependentNotesFluidParsedMessages = '';
        if ($this->dependentNotesFluidParsedMessages instanceof \ArrayAccess) {
            foreach ($this->dependentNotesFluidParsedMessages as $dependentNotesFluidParsedMessage) {
                $dependentNotesFluidParsedMessages .= "<p>{$dependentNotesFluidParsedMessage}</p>";
            }
        }

        return $dependentNotesFluidParsedMessages;
    }

    /**
     * @return string $pricing
     */
    public function getPricing()
    {
        return '';
        #return \S3b0\EcomConfigCodeGenerator\Utility\PriceHandler::getPriceInCurrency($this->pricingNumeric, $this->configuration->getCurrency(), TRUE);
    }

    /**
     * @param string $pricing
     */
    public function setPricing($pricing)
    {
        $this->pricing = $pricing;
    }

    /**
     * @return string $pricingNumeric
     */
    public function getPricingNumeric()
    {
        return number_format($this->pricingNumeric, 2);
    }

    /**
     * @param float $pricingNumeric
     */
    public function setPricingNumeric($pricingNumeric)
    {
        $this->pricingNumeric = $pricingNumeric;
    }

    /**
     * Adds a part to active items
     *
     * @param \S3b0\EcomSkuGenerator\Domain\Model\Part $part
     * @return void
     */
    public function addActivePart(\S3b0\EcomSkuGenerator\Domain\Model\Part $part)
    {
        if (!$this->activeParts instanceof \TYPO3\CMS\Extbase\Persistence\ObjectStorage) {
            $this->activeParts = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        }
        if (!$this->activeParts->contains($part)) {
            $this->activeParts->attach($part);
        }
    }

    /**
     * Removes a part from active items
     *
     * @param \S3b0\EcomSkuGenerator\Domain\Model\Part $partToRemove The Part to be removed
     * @return void
     */
    public function removeActivePart(\S3b0\EcomSkuGenerator\Domain\Model\Part $partToRemove)
    {
        if ($this->activeParts instanceof \TYPO3\CMS\Extbase\Persistence\ObjectStorage && $this->activeParts->contains($partToRemove)) {
            $this->activeParts->detach($partToRemove);
        }
    }

    /**
     * Returns the activeParts
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\Part> $activeParts
     */
    public function getActiveParts()
    {
        return $this->activeParts;
    }

    /**
     * Sets the activeParts
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\Part> $activeParts
     * @return void
     */
    public function setActiveParts(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $activeParts)
    {
        $this->activeParts = $activeParts;
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
     * @return void
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return bool $current
     */
    public function isCurrent()
    {
        return $this->current;
    }

    /**
     * @param bool $current
     */
    public function setCurrent($current)
    {
        $this->current = $current;
    }

    /**
     * @return \S3b0\EcomSkuGenerator\Domain\Model\PartGroup
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * @param \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $next
     */
    public function setNext(\S3b0\EcomSkuGenerator\Domain\Model\PartGroup $next = null)
    {
        $this->next = $next;
    }

    /**
     * @return bool
     */
    public function isUnlocked()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isVisibleInSummary()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isVisibleInNavigation()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isMultipleSelectable()
    {
        return ($this->settings & Setup::BIT_PARTGROUP_MULTIPLE_SELECT) === Setup::BIT_PARTGROUP_MULTIPLE_SELECT;
    }

    /**
     * @param \S3b0\EcomSkuGenerator\Domain\Model\Currency $currency
     * @param array $settings
     */
    public function setPartsCurrencyPricing(\S3b0\EcomConfigCodeGenerator\Domain\Model\Currency $currency = null, array $settings = []) {
        if ($this->parts instanceof \TYPO3\CMS\Extbase\Persistence\ObjectStorage && $this->parts->count()) {
            /** @var \S3b0\EcomSkuGenerator\Domain\Model\Part $part */
            foreach ($this->parts as $part) {
                if ($part->isActive()) {
                    $this->pricingNumeric += $part->getNoCurrencyPricing();
                }
            }
        }
    }

    /**
     * @return int $stepIndicator
     */
    public function getStepIndicator()
    {
        return $this->stepIndicator;
    }

    /**
     * @param int $stepIndicator
     */
    public function setStepIndicator($stepIndicator)
    {
        $this->stepIndicator = $stepIndicator;
    }

    /**
     * @return bool $last
     */
    public function isLast()
    {
        return $this->last;
    }

    /**
     * @param bool $last
     */
    public function setLast($last)
    {
        $this->last = $last;
    }

    /**
     * reset function
     */
    public function reset()
    {
        $this->dependentNotesFluidParsedMessages = new \ArrayObject();
        $this->active = false;
        $this->current = false;
        $this->next = null;
        $this->selectable = true;
        $this->last = false;
    }

}