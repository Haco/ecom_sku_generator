<?php
namespace S3b0\EcomSkuGenerator\Controller;

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
use S3b0\EcomSkuGenerator\Domain\Model\PartGroup;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * GeneratorController
 */
class GeneratorController extends \S3b0\EcomSkuGenerator\Controller\BaseController
{

    /**
     * action index
     *
     * @param \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $partGroup
     * @return void
     */
    public function indexAction(\S3b0\EcomSkuGenerator\Domain\Model\PartGroup $partGroup = null)
    {
        $this->view->assign('value', $this->getIndexActionData(func_get_args()));
    }

    /**
     * @param array $arguments
     * @return array
     */
    public function getIndexActionData(array $arguments = [])
    {
        $checkIfPartGroupArgumentIsSet = $arguments[0] instanceof \S3b0\EcomSkuGenerator\Domain\Model\PartGroup;
        // Get current configuration (Array: options=array(options)|packages=array(package => option(s)))
        $configuration = $this->feSession->get('config') ?: [];
        $partGroups = $this->initializePartGroups(
            $this->contentObject->getSkuGeneratorPartGroups() ?: new \TYPO3\CMS\Extbase\Persistence\ObjectStorage(),
            $configuration,
            $currentPartGroup,
            $progress
        );
        if ($arguments[0] instanceof \S3b0\EcomSkuGenerator\Domain\Model\PartGroup) {
            /** @var \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $currentPartGroup */
            $currentPartGroup = $arguments[0];
        }
        if ($currentPartGroup instanceof \S3b0\EcomSkuGenerator\Domain\Model\PartGroup) {
            $currentPartGroup->setCurrent(true);
            $this->initializeParts(
                $currentPartGroup->getParts(),
                $currentPartGroup,
                $configuration
            );
        }

        $jsonData = [
            'title' => '',
            'instructions' => $this->contentObject->getBodytext(),
            'configuration' => $configuration,
            'progress' => $progress,
            'progressPercentage' => $progress * 100,
            'partGroups' => $partGroups,
            'currentPartGroup' => $currentPartGroup,
            'nextPartGroup' => $currentPartGroup instanceof \S3b0\EcomSkuGenerator\Domain\Model\PartGroup && $currentPartGroup->getNext() instanceof \S3b0\EcomSkuGenerator\Domain\Model\PartGroup ? $currentPartGroup->getNext()->getUid() : 0,
            'showResultingConfiguration' => $progress === 1 && !$checkIfPartGroupArgumentIsSet
        ];

        /** LAST & FINAL RESULT STEP */
        /** Gets final result data when end of configurator reached */
        if ($progress === 1 && is_array($configuration) && sizeof($configuration)) {
            $jsonData['noConfigurationFound'] = false;
            /** @var \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $configurations */
            $configurations = $this->configurationRepository->findByConfigurationArray($configuration);
            /** Finds the final configuration (SKU) */
            if ($configurations instanceof \Countable && $configurations->count() === 1) {
                $jsonData['title'] = $configurations->getFirst()->getTitle();
                if ($configurations->getFirst()->isLiableToPayCosts()) {
                    $configurations->getFirst()->setCurrency($this->currency);
                    $this->contentObject->setConfigurationPrice($configurations->getFirst()->getNoCurrencyPricing($this->currency));
                }
            } else {
                /**
                 * No suitable article (SKU) with current configuration found.
                 **/
                $jsonData['title'] = LocalizationUtility::translate('noArticleFound','ecom_sku_generator') . '.';
                $jsonData['noConfigurationFound'] = true;
            }
            $jsonData['configurationCode'] = $this->getSku($configurations, $configuration);
        }

        if ($this->request->getControllerName() === 'AjaxRequest') {
            $jsonData['selectPartsHTML'] = $currentPartGroup instanceof \S3b0\EcomSkuGenerator\Domain\Model\PartGroup ? $this->getPartSelectorHTML($currentPartGroup->getParts()) : null;
            $jsonData['selectPartGroupsHTML'] = $this->getPartGroupSelectorHTML($partGroups);
        }

        if ($this->pricing) {
            $jsonData['configurationPrice'] = $this->contentObject->getConfigurationPriceFormatted();
        }

        return $jsonData;
    }

    /**
     * action currencySelect
     *
     * @return void
     */
    public function currencySelectAction()
    {
        $this->view->assign('currencies', $this->currencyRepository->findAll());
    }

    /**
     * @param \S3b0\EcomConfigCodeGenerator\Domain\Model\Currency $currency
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     */
    public function setCurrencyAction(\S3b0\EcomConfigCodeGenerator\Domain\Model\Currency $currency)
    {
        $this->feSession->store('currency', $currency->getUid(), 'ecom');
        $this->redirect('index');
    }

    /**
     * action reset
     *
     * @return void
     */
    public function resetAction()
    {
    }

}