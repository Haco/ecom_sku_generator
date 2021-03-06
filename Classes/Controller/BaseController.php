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

use S3b0\EcomSkuGenerator\Setup;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class BaseController
 * @package S3b0\EcomConfigCodeGenerator\Controller
 */
class BaseController extends \Ecom\EcomToolbox\Controller\ActionController
{

    /**
     * @var \S3b0\EcomSkuGenerator\Domain\Model\Content
     */
    protected $contentObject = null;

    /**
     * feSession
     *
     * @var \Ecom\EcomToolbox\Domain\Session\FrontendSessionHandler
     * @inject
     */
    public $feSession;

    /**
     * contentRepository
     *
     * @var \S3b0\EcomSkuGenerator\Domain\Repository\ContentRepository
     * @inject
     */
    protected $contentRepository;

    /**
     * contentRepository
     *
     * @var \S3b0\EcomSkuGenerator\Domain\Repository\ConfigurationRepository
     * @inject
     */
    protected $configurationRepository;

    /**
     * partGroupRepository
     *
     * @var \S3b0\EcomSkuGenerator\Domain\Repository\PartGroupRepository
     * @inject
     */
    protected $partGroupRepository;

    /**
     * partRepository
     *
     * @var \S3b0\EcomSkuGenerator\Domain\Repository\PartRepository
     * @inject
     */
    protected $partRepository;

    /**
     * currencyRepository
     *
     * @var \S3b0\EcomConfigCodeGenerator\Domain\Repository\CurrencyRepository
     * @inject
     */
    protected $currencyRepository;

    /**
     * logRepository
     *
     * @var \S3b0\EcomSkuGenerator\Domain\Repository\LogRepository
     * @inject
     */
    protected $logRepository;

    /**
     * frontendUserRepository
     *
     * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
     * @inject
     */
    protected $frontendUserRepository;

    /**
     * regionRepository
     *
     * @var \Ecom\EcomToolbox\Domain\Repository\RegionRepository
     * @inject
     */
    protected $regionRepository;

    /**
     * stateRepository
     *
     * @var \Ecom\EcomToolbox\Domain\Repository\StateRepository
     * @inject
     */
    protected $stateRepository;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomSkuGenerator\Domain\Model\Configuration>
     */
    protected $configurations;

    /**
     * @var bool Indicate if pricing is active or not
     */
    protected $pricing = false;

    /**
     * @var bool Allows custom configuration and ignores incompatible parts.
     */
    protected $allowCustomConfiguration = false;

    /**
     * @var \S3b0\EcomConfigCodeGenerator\Domain\Model\Currency
     */
    protected $currency = null;

    /**
     * Initializes the controller before invoking an action method.
     *
     * Override this method to solve tasks which all actions have in
     * common.
     *
     * @return void
     * @api
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     */
    public function initializeAction()
    {
        // Fetch content object
        $this->contentObject = $this->contentObject ?: $this->contentRepository->findByUid($this->configurationManager->getContentObject()->data['uid']);
        if (!$this->contentObject instanceof \S3b0\EcomSkuGenerator\Domain\Model\Content) {
            $this->throwStatus(404, null, '<h1>' . LocalizationUtility::translate('404.noContentObject', 'ecom_config_code_generator') . '</h1>' . LocalizationUtility::translate('404.message.noContentObject', $this->extensionName, ["<a href=\"mailto:{$this->settings['webmasterEmail']}\">{$this->settings['webmasterEmail']}</a>"]));
        }
        // Fetch configuration
        $this->configurations = $this->contentObject->getSkuGeneratorConfigurations();
        if (!$this->configurations instanceof \TYPO3\CMS\Extbase\Persistence\ObjectStorage && !$this->configurations instanceof \TYPO3\CMS\Extbase\Persistence\Generic\LazyObjectStorage) {
            $this->throwStatus(404, null, '<h1>' . LocalizationUtility::translate('404.noConfiguration', 'ecom_config_code_generator') . '</h1>' . LocalizationUtility::translate('404.message.noConfiguration', $this->extensionName, ["<a href=\"mailto:{$this->settings['webmasterEmail']}\">{$this->settings['webmasterEmail']}</a>"]));
        }
        if (!($this->contentObject->getSkuGeneratorPartGroups() instanceof \TYPO3\CMS\Extbase\Persistence\ObjectStorage || $this->contentObject->getSkuGeneratorPartGroups() instanceof \TYPO3\CMS\Extbase\Persistence\Generic\LazyObjectStorage) || !$this->contentObject->getSkuGeneratorPartGroups()->count()) {
            $this->throwStatus(404, null, '<h1>' . LocalizationUtility::translate('404.noPartGroups', 'ecom_config_code_generator') . '</h1>' . LocalizationUtility::translate('404.message.noPartGroups', $this->extensionName, ["<a href=\"mailto:{$this->settings['webmasterEmail']}\">{$this->settings['webmasterEmail']}</a>"]));
        }

        $this->pricing = $this->contentObject->isPricingEnabled() && $GLOBALS['TSFE']->loginUser && \Ecom\EcomToolbox\Security\Frontend::checkForUserRoles($this->settings['accessPricing']);
        $this->allowCustomConfiguration = $this->contentObject->getSkuGeneratorAllowCustomConfig();

        // Frontend-Session
        $this->feSession->setStorageKey(Setup::getSessionStorageKey($this->contentObject));
        // On reset destroy config session data
        if ($this->request->getControllerName() === 'Generator' && $this->request->getControllerActionName() === 'reset') {
            $this->feSession->delete('config');
            $this->feSession->delete('min-order-quantity');
            $resetUri = $this->uriBuilder->reset()->setArguments(['L' => $GLOBALS['TSFE']->sys_language_uid])->setUseCacheHash(false)->uriFor('index', array(), 'Generator');
            $this->redirectToUri($resetUri);
        }
        // Redirect to currency selection if pricing enabled
        if ($this->pricing && !in_array($this->request->getControllerActionName(), ['currencySelect', 'setCurrency']) && !$this->feSession->get('currency', 'ecom')) {
            $this->redirect('currencySelect', 'Generator');
        }
        if ($this->pricing && $this->feSession->get('currency', 'ecom') && MathUtility::canBeInterpretedAsInteger($this->feSession->get('currency', 'ecom'))) {
            $this->currency = $this->currencyRepository->findByUid($this->feSession->get('currency', 'ecom'));
        } else {
            $this->currency = $this->currencyRepository->getDefault();
        }
        $this->contentObject->setCurrency($this->currency);
    }

    /**
     * Initializes the view before invoking an action method.
     *
     * Override this method to solve assign variables common for all actions
     * or prepare the view in another way before the action is called.
     *
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view The view to be initialized
     *
     * @return void
     * @api
     */
    public function initializeView(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view)
    {
        $this->view->assignMultiple([
            'contentObject' => $this->contentObject,
            'pricingEnabled' => $this->pricing,
            'allowCustomConfig' => $this->allowCustomConfiguration,
            'jsData' => [
                'pageId' => $GLOBALS['TSFE']->id,
                'controller' => $this->request->getControllerName(),
                'sysLanguage' => (int)$GLOBALS['TSFE']->sys_language_content,
                'contentObject' => $this->contentObject->_getProperty('_localizedUid')
            ]
        ]);
        if ($this->pricing) {
            $this->view->assignMultiple([
                'currencyActive' => $this->currency,
                'currencies' => $this->currencyRepository->findAll()
            ]);
        }
    }

    /**
     * Initialize part groups
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $partGroups
     * @param array $configuration
     * @param \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $current
     * @param integer $progress
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    protected function initializePartGroups(
        \TYPO3\CMS\Extbase\Persistence\ObjectStorage $partGroups,
        array &$configuration,
        \S3b0\EcomSkuGenerator\Domain\Model\PartGroup &$current = null,
        &$progress = 0
    ) {
        /** @var \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $current */
        $current = null;   // Current part group
        $previous = null;  // Previous part group (NEXT as of array_reverse)
        $cycle = 1;        // Count loop cycles
        $locked = 0;       // Count locked items, still visible!

        /** @var array $configuredParts Create an array containing all configured part for validation */
        $configuredParts = [];
        if (sizeof($configuration)) {
            foreach ($configuration as $partGroupParts) {
                $configuredParts = array_merge($configuredParts, (array)$partGroupParts);
                foreach ($partGroupParts as $uid) {
                    /** @var \S3b0\EcomSkuGenerator\Domain\Model\Part $part */
                    $part = $this->partRepository->findByUid($uid);
                    if ($part instanceof \S3b0\EcomSkuGenerator\Domain\Model\Part) {
                        $part->setActive(true);
                        if ($part->isLiableToPayCosts()) {
                            $part->setCurrency($this->currency);
                            $this->contentObject->sumUpConfigurationPrice($part->getNoCurrencyPricing());
                        }
                    } else {
                        $this->redirect('reset');
                    }
                }
            }
        }

        /** @var \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $partGroup */
        foreach (array_reverse($partGroups->toArray()) as $partGroup) {
            $partGroup->reset();
            /**
             * Add dependent notes, if any.
             * Dependent notes may appear if a trigger part has been added to configuration.
             */
            if ($partGroup->getDependentNotes() instanceof \Countable && $partGroup->getDependentNotes()->count()) {
                /** @var \S3b0\EcomSkuGenerator\Domain\Model\DependentNote $dependentNote */
                foreach ($partGroup->getDependentNotes() as $dependentNote) {
                    if ($dependentNote->getDependentParts()->count()) {
                        $logicalAnd = $dependentNote->isUseLogicalAnd();
                        $addMessage = false;
                        /** @var \S3b0\EcomSkuGenerator\Domain\Model\Part $dependentPart */
                        foreach ($dependentNote->getDependentParts() as $dependentPart) {
                            if (is_array($configuredParts) && in_array($dependentPart->getUid(), $configuredParts)) {
                                $addMessage = true;
                                if (!$logicalAnd) {
                                    break;
                                }
                            } else {
                                if ($logicalAnd) {
                                    $addMessage = false;
                                    break;
                                }
                            }
                        }
                        if ($addMessage) {
                            $partGroup->addDependentNotesFluidParsedMessage($dependentNote->getNote());
                        }
                    }
                }
            }
            /**
             * Check for active packages, set corresponding sate and fill ObjectStorage
             */
            if (array_key_exists($partGroup->getUid(),
                    $configuration) && is_array($configuration[$partGroup->getUid()])
            ) {
                // First of all check dependencies and unset parts, if not valid anymore
                foreach ($configuration[$partGroup->getUid()] as $partUid) {
                    /** @var \S3b0\EcomSkuGenerator\Domain\Model\Part $part */
                    if ($part = $this->partRepository->findByUid($partUid)) {
                        \S3b0\EcomSkuGenerator\Session\ManageConfiguration::addPartToConfiguration($this, $part,
                            $configuration);
                    }
                }
            }
            $partGroup->setNext($previous);
            /** @var \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $previous */
            $previous = $partGroup;
            $cycle++;
        }
        $cycle = 0;
        foreach ($partGroups as $partGroup) {
            $partGroup->setStepIndicator(++$cycle);
            /** SET PRICE */
            $partGroup->setPartsCurrencyPricing($this->currency, $this->settings);
            if (!array_key_exists($partGroup->getUid(),
                    $configuration) && !is_array($configuration[$partGroup->getUid()]) && !$current instanceof \S3b0\EcomSkuGenerator\Domain\Model\PartGroup
            ) {
                $current = $partGroup;
            }
        }
        $partGroup->setLast(true);
        $this->feSession->store('config', $configuration);

        // Get progress state update (ratio of active to visible packages) => float from 0 to 1 (*100 = %)
        $progress = (sizeof($configuration) - $locked) / ($partGroups->count() - $locked);

        return $partGroups;
    }

    /**
     * Initialize parts
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $parts
     * @param \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $partGroup
     * @param array $configuration
     * @return void
     */
    public function initializeParts(
        \TYPO3\CMS\Extbase\Persistence\ObjectStorage &$parts,
        \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $partGroup,
        array $configuration
    ) {
        /**
         * Handling of compatible or incompatible Parts
         * When there are already selected parts and when not
         */
        /** @var \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $configurations */
        if (sizeof($configuration) && ($compatibleParts = $this->configurationRepository->findCompatiblePartsByConfigurationArray($configuration, $partGroup))) {
            /** @var \S3b0\EcomSkuGenerator\Domain\Model\Part $part */
            foreach ($compatibleParts as $part) {
                $part->setCompatibleToSelection(true);
            }
        } else {
            /** Happens when no part is selected at all. User is at the first step of configuration */
            /** @var \S3b0\EcomSkuGenerator\Domain\Model\Part $part */
            $availableParts = $this->configurationRepository->getAvailablePartsInAnyConfiguration($parts);
            foreach ($parts as $part) {
                if (in_array($part->getUid(), $availableParts)) {
                    $part->setCompatibleToSelection(true);
                } else {
                    $part->setCompatibleToSelection(false);
                }
            }
        }


        /** @var \S3b0\EcomSkuGenerator\Domain\Model\Part $part */
        foreach ($parts as $part) {
            $part->setCurrency($this->currency);
        }
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $configurations
     * @param array $configuration
     * @return array
     */
    protected function getSku($configurations, $configuration)
    {
        $minOrderQuantityHtml = '';
        $summaryTableRows = [];
        $summaryTableMailRows = [];
        $code = [];
        $blankCode = [];
        $minOrderQuantity = 0;

        /** @var \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $partGroup */
        foreach ($this->contentObject->getSkuGeneratorPartGroups() as $partGroup) {
            $parts = $configuration[$partGroup->getUid()];
            ksort($parts); // Order by sorting
            $partList = [];
            foreach ($parts as $partUid) {
                /** @var \S3b0\EcomSkuGenerator\Domain\Model\Part $part */
                $part = $this->partRepository->findByUid($partUid);
                $partTitle = $part->getTitle();
                if ($part->getMinOrderQuantity()) {
                    $partTitle .= ' <small class="text-primary">(MOQ: ' . (int)$part->getMinOrderQuantity() . ')</small>';
                }
                $partList[] = $partTitle;
                /** Find & set global min order quantity for current configuration */
                if ($part->getMinOrderQuantity()) {
                    $minOrderQuantity = ($part->getMinOrderQuantity() > $minOrderQuantity) ? $part->getMinOrderQuantity() : $minOrderQuantity;
                }
            }
            $summaryTableRows[] = ("
					<td>{$partGroup->getStepIndicator()}</td>
					<td>{$partGroup->getTitle()}</td>
					<td>" . implode(', ', $partList) . "</td>
					<td><a data-part-group=\"{$partGroup->getUid()}\" class=\"generator-part-group-select\"><i class=\"fa fa-pencil\"></i></a></td>
				") . ($this->pricing ? "<td style=\"text-align:right\">" . \S3b0\EcomSkuGenerator\Utility\PriceHandler::getPriceInCurrency($partGroup->getPricingNumeric(),
                        $this->currency, true) . "</td>" : "");

            $summaryTableMailRows[] = ("
					<td>{$partGroup->getTitle()}</td>
					<td>" . implode(', ', $partList) . "</td>
				");
        }

        // Prepare Minimum Order Quantity for Result & Request
        if ($minOrderQuantity) {
            $this->feSession->store('min-order-quantity', $minOrderQuantity);
            $minOrderQuantityHtml = '
            <div class="text-center sku-generator-min-order-quantity-result">
                <span class="label label-primary">' . LocalizationUtility::translate('minOrderQuantity', 'ecom_sku_generator') . ': <strong>' . $minOrderQuantity . '</strong></span>
            </div>';
        } else {
            $this->feSession->delete('min-order-quantity');
        }

        /** If Configuration/SKU is found */
        if ($configurations instanceof \Countable && $configurations->count() === 1) {
            ksort($code);      // Order code either incremental or by place in code
            ksort($blankCode); // Order code either incremental or by place in code

            return [
                'title' => $configurations->getFirst()->getTitle(),
                'code' => $configurations->getFirst()->getSku(),
                'blankCode' => $configurations->getFirst()->getSku(),
                'minOrderQuantityHtml' => $this->sanitize_output($minOrderQuantityHtml),
                'summaryTable' => $this->sanitize_output('<table><tr>' . implode('</tr><tr>',
                        $summaryTableRows) . '</tr></table>'),
                'summaryTableMail' => $this->sanitize_output('<table><tr>' . implode('</tr><tr>',
                        $summaryTableMailRows) . '</tr></table>')
            ];
        } else {
            return [
                'title' => $this->contentObject->getHeader() . ' ' . LocalizationUtility::translate('individualInquiry', 'ecom_sku_generator'),
                'minOrderQuantityHtml' => $this->sanitize_output($minOrderQuantityHtml),
                'code' => LocalizationUtility::translate('individualInquiry', 'ecom_sku_generator'),
                'blankCode' => LocalizationUtility::translate('noArticleFound','ecom_sku_generator') . '.',
                'summaryTable' => $this->sanitize_output('<table><tr>' . implode('</tr><tr>',
                        $summaryTableRows) . '</tr></table>'),
                'summaryTableMail' => $this->sanitize_output('<table><tr>' . implode('</tr><tr>',
                        $summaryTableMailRows) . '</tr></table>')
            ];
        }
    }

    /**
     * @param \S3b0\EcomSkuGenerator\Domain\Model\Log $log
     * @param array $configuration
     */
    protected function addConfigurationToLog(\S3b0\EcomSkuGenerator\Domain\Model\Log &$log, array $configuration)
    {
        $ipLength = !MathUtility::canBeInterpretedAsInteger($this->settings['log']['ipLength']) || $this->settings['log']['ipLength'] > 4 ? 4 : $this->settings['log']['ipLength'];

        $configurations = $this->configurationRepository->findByConfigurationArray($configuration);
        $this->initializePartGroups($this->contentObject->getSkuGeneratorPartGroups(), $configuration);

        /** @var \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $partGroup */
        foreach ($this->contentObject->getSkuGeneratorPartGroups() as $partGroup) {
            $parts = $configuration[$partGroup->getUid()];
            ksort($parts); // Order by sorting
            foreach ($parts as $partUid) {
                /** @var \S3b0\EcomSkuGenerator\Domain\Model\Part $part */
                $part = $this->partRepository->findByUid($partUid);
                $log->addConfiguredPartSku($part);
            }
        }

        /** Set minimum quantity */
        if ($log->getQuantity() === 0) {
            $log->setQuantity(1);
        }
        $log->setSessionId($GLOBALS['TSFE']->fe_user->id)
            ->maskIpAddress($ipLength)
            ->setPricing($this->contentObject->getConfigurationPriceFormatted())
            ->setPid(0);

        /** If Configuration/SKU is found add it to log, else leave empty and set to "no articles found" */
        if ($configurations instanceof \Countable && $configurations->count() === 1) {
            $log->setConfiguration($configurations->getFirst()->getSku());
        } else {
            $log->setConfiguration($this->contentObject->getHeader());
            $log->setIncompatibleConfig(true);
        }

        if ($GLOBALS['TSFE']->loginUser) {
            /** @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser $feUser */
            $feUser = $this->frontendUserRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
            $log->setFeUser($feUser);
        }
    }

    /**
     * @param string $templateName template name (UpperCamelCase)
     * @param array $variables variables to be passed to the Fluid view
     *
     * @return string
     */
    protected function getStandAloneTemplate($templateName, array $variables = [])
    {
        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
        $view = $this->objectManager->get(\TYPO3\CMS\Fluid\View\StandaloneView::class);

        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        $templateRootPath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['templateRootPath'] ?: end($extbaseFrameworkConfiguration['view']['templateRootPaths']));
        $partialRootPath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['partialRootPath'] ?: end($extbaseFrameworkConfiguration['view']['partialRootPaths']));
        $templatePathAndFilename = "{$templateRootPath}{$templateName}.html";
        $view->setTemplatePathAndFilename($templatePathAndFilename);
        $view->setPartialRootPaths([$partialRootPath]);
        $view->assignMultiple($variables);
        $view->setFormat('html');

        return $this->sanitize_output($view->render());
    }

    /**
     * Minify All Output - based on the search and replace regexes.
     * @param string $buffer Input string
     * @return string
     */
    protected function sanitize_output($buffer)
    {
        $search = [
            '/\>[^\S ]+/s', //strip whitespaces after tags, except space
            '/[^\S ]+\</s', //strip whitespaces before tags, except space
            '/(\s)+/s'  // shorten multiple whitespace sequences
        ];
        $replace = [
            '>',
            '<',
            '\\1'
        ];
        $buffer = preg_replace($search, $replace, $buffer);

        return $buffer;
    }

}