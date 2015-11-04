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
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class BaseController
 * @package S3b0\EcomConfigCodeGenerator\Controller
 */
class BaseController extends \Ecom\EcomToolbox\Controller\ActionController {

	/**
	 * @var \S3b0\EcomSkuGenerator\Domain\Model\Content
	 */
	protected $contentObject = NULL;

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
	 * @var \S3b0\EcomConfigCodeGenerator\Domain\Repository\LogRepository
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
	protected $pricing = FALSE;

	/**
	 * @var \S3b0\EcomConfigCodeGenerator\Domain\Model\Currency
	 */
	protected $currency = NULL;

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
	public function initializeAction() {
		// Fetch content object
		$this->contentObject = $this->contentObject ?: $this->contentRepository->findByUid($this->configurationManager->getContentObject()->data['uid']);
		if ( !$this->contentObject instanceof \S3b0\EcomSkuGenerator\Domain\Model\Content )
			$this->throwStatus(404, NULL, '<h1>' . LocalizationUtility::translate('404.noContentObject', 'ecom_config_code_generator') . '</h1>' . LocalizationUtility::translate('404.message.noContentObject', $this->extensionName, [ "<a href=\"mailto:{$this->settings['webmasterEmail']}\">{$this->settings['webmasterEmail']}</a>" ]));
		// Fetch configuration
		$this->configurations = $this->contentObject->getSkuGeneratorConfigurations();
		if ( !$this->configurations instanceof \TYPO3\CMS\Extbase\Persistence\ObjectStorage && !$this->configurations instanceof \TYPO3\CMS\Extbase\Persistence\Generic\LazyObjectStorage )
			$this->throwStatus(404, NULL, '<h1>' . LocalizationUtility::translate('404.noConfiguration', 'ecom_config_code_generator') . '</h1>' . LocalizationUtility::translate('404.message.noConfiguration', $this->extensionName, [ "<a href=\"mailto:{$this->settings['webmasterEmail']}\">{$this->settings['webmasterEmail']}</a>" ]));
		if ( !($this->contentObject->getSkuGeneratorPartGroups() instanceof \TYPO3\CMS\Extbase\Persistence\ObjectStorage || $this->contentObject->getSkuGeneratorPartGroups() instanceof \TYPO3\CMS\Extbase\Persistence\Generic\LazyObjectStorage) || !$this->contentObject->getSkuGeneratorPartGroups()->count() )
			$this->throwStatus(404, NULL, '<h1>' . LocalizationUtility::translate('404.noPartGroups', 'ecom_config_code_generator') . '</h1>' . LocalizationUtility::translate('404.message.noPartGroups', $this->extensionName, [ "<a href=\"mailto:{$this->settings['webmasterEmail']}\">{$this->settings['webmasterEmail']}</a>" ]));

		$this->pricing = $this->contentObject->isPricingEnabled() && $GLOBALS['TSFE']->loginUser && \Ecom\EcomToolbox\Security\Frontend::checkForUserRoles($this->settings['accessPricing']);
$this->pricing = TRUE;
		// Frontend-Session
		$this->feSession->setStorageKey(Setup::getSessionStorageKey($this->contentObject));
		// On reset destroy config session data
		if ( $this->request->getControllerName() === 'Generator' && $this->request->getControllerActionName() === 'reset' ) {
			$this->feSession->delete('config');
			$this->redirect('index', 'Generator');
		}
		// Redirect to currency selection if pricing enabled
		if ( $this->pricing && !in_array($this->request->getControllerActionName(), [ 'currencySelect', 'setCurrency' ]) && !$this->feSession->get('currency', 'ecom') )
			$this->redirect('currencySelect', 'Generator');
		if ( $this->pricing && $this->feSession->get('currency', 'ecom') && MathUtility::canBeInterpretedAsInteger($this->feSession->get('currency', 'ecom')) ) {
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
	public function initializeView(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view) {
		$this->view->assignMultiple([
			'contentObject' => $this->contentObject,
			'pricingEnabled' => $this->pricing,
			'jsData' => [
				'pageId' => $GLOBALS['TSFE']->id,
				'controller' => $this->request->getControllerName(),
				'sysLanguage' => (int) $GLOBALS['TSFE']->sys_language_content,
				'contentObject' => $this->contentObject->_getProperty('_localizedUid')
			]
		]);
		if ( $this->pricing ) {
			$this->view->assignMultiple([
				'currencyActive' => $this->currency,
				'currencies' => $this->currencyRepository->findAll()
			]);
		}
	}

	/**
	 * Initialize part groups
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage  $partGroups
	 * @param array                                         $configuration
	 * @param \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $current
	 * @param integer                                       $progress
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
	 */
	protected function initializePartGroups(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $partGroups, array &$configuration, \S3b0\EcomSkuGenerator\Domain\Model\PartGroup &$current = NULL, &$progress = 0) {
		/** @var \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $current */
		$current = NULL;   // Current part group
		$previous = NULL;  // Previous part group (NEXT as of array_reverse)
		$cycle = 1;        // Count loop cycles
		$locked = 0;       // Count locked items, still visible!

		/** @var array $configuredParts Create an array containing all configured part for validation */
		$configuredParts = [ ];
		if ( sizeof($configuration) ) {
			foreach ( $configuration as $partGroupParts ) {
				$configuredParts = array_merge($configuredParts, (array) $partGroupParts);
				if ( $this->pricing ) {
					foreach ( $partGroupParts as $uid ) {
						/** @var \S3b0\EcomSkuGenerator\Domain\Model\Part $part */
						$part = $this->partRepository->findByUid($uid);
						if ( $part->isLiableToPayCosts() ) {
							$part->setCurrency($this->currency);
							$this->contentObject->sumUpConfigurationPrice($part->getNoCurrencyPricing());
						}
					}
				}
			}
		}

		/** @var \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $partGroup */
		foreach ( array_reverse($partGroups->toArray()) as $partGroup ) {
			$partGroup->reset();
			/**
			 * Add dependent notes, if any.
			 * Dependent notes may appear if a trigger part has been added to configuration.
			 */
			if ( $partGroup->getDependentNotes() instanceof \Countable && $partGroup->getDependentNotes()->count() ) {
				/** @var \S3b0\EcomSkuGenerator\Domain\Model\DependentNote $dependentNote */
				foreach ( $partGroup->getDependentNotes() as $dependentNote ) {
					if ( $dependentNote->getDependentParts()->count() ) {
						$logicalAnd = $dependentNote->isUseLogicalAnd();
						$addMessage = FALSE;
						/** @var \S3b0\EcomSkuGenerator\Domain\Model\Part $dependentPart */
						foreach ( $dependentNote->getDependentParts() as $dependentPart ) {
							if ( is_array($configuredParts) && in_array($dependentPart->getUid(), $configuredParts) ) {
								$addMessage = TRUE;
								if ( !$logicalAnd ) {
									break;
								}
							} else {
								if ( $logicalAnd ) {
									$addMessage = FALSE;
									break;
								}
							}
						}
						if ( $addMessage ) {
							$partGroup->addDependentNotesFluidParsedMessage($dependentNote->getNote());
						}
					}
				}
			}
			/**
			 * Check for active packages, set corresponding sate and fill ObjectStorage
			 */
			if ( array_key_exists($partGroup->getUid(), $configuration) && is_array($configuration[$partGroup->getUid()]) ) {
				// First of all check dependencies and unset parts, if not valid anymore
				foreach ( $configuration[$partGroup->getUid()] as $partUid ) {
					/** @var \S3b0\EcomSkuGenerator\Domain\Model\Part $part */
					if ( $part = $this->partRepository->findByUid($partUid) ) {
						\S3b0\EcomSkuGenerator\Session\ManageConfiguration::addPartToConfiguration($this, $part, $configuration);
					}
				}
			}
			$partGroup->setNext($previous);
			/** @var \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $previous */
			$previous = $partGroup;
			$cycle++;
		}
		$cycle = 0;
		foreach ( $partGroups as $partGroup ) {
			$partGroup->setStepIndicator(++$cycle);
			/** SET PRICE */
			$partGroup->setPartsCurrencyPricing($this->currency, $this->settings);
			#$this->correctNextPartGroupIfItHasBeenAffectedByAutoSetPartsOrSimilar($partGroup);
			if ( !array_key_exists($partGroup->getUid(), $configuration) && !is_array($configuration[$partGroup->getUid()]) && !$current instanceof \S3b0\EcomSkuGenerator\Domain\Model\PartGroup ) {
				$current = $partGroup;
			}
		}
		$partGroup->setLast(TRUE);
		$this->feSession->store('config', $configuration);

		// Get progress state update (ratio of active to visible packages) => float from 0 to 1 (*100 = %)
		$progress = ( sizeof($configuration) - $locked ) / ( $partGroups->count() - $locked );

		return $partGroups;
	}

	/**
	 * Traverse setting correct 'next' item, skipping locked
	 * @param \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $partGroup
	 */
	private function correctNextPartGroupIfItHasBeenAffectedByAutoSetPartsOrSimilar(\S3b0\EcomSkuGenerator\Domain\Model\PartGroup $partGroup = NULL, $traverse = 0) {
		$next = $partGroup->getNext();
		if ( $traverse > 0 ) {
			for ($i = 0; $i < $traverse; $i++) {
				if ( $next instanceof \S3b0\EcomSkuGenerator\Domain\Model\PartGroup ) {
					$next = $next->getNext();
				} else {
					$next = NULL;
					continue;
				}
			}
		}
		if ( $next instanceof \S3b0\EcomSkuGenerator\Domain\Model\PartGroup ) {
			if ( $next->isUnlocked() ) {
				$partGroup->setNext($next);
			} elseif ( $next->getNext() instanceof \S3b0\EcomSkuGenerator\Domain\Model\PartGroup ) {
				$this->correctNextPartGroupIfItHasBeenAffectedByAutoSetPartsOrSimilar($partGroup, ++$traverse);
			}
		} else {
			$partGroup->setNext(NULL);
		}
	}

	/**
	 * @param \S3b0\EcomSkuGenerator\Controller\BaseController $controller Ensure an Instance of extensions
	 *                                                                     BaseController is given to provide
	 *                                                                     necessary injections
	 * @param array                                            $list
	 *
	 *@return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
	 */
	protected static function getAndSetActivePartsForPartGroup(\S3b0\EcomSkuGenerator\Controller\BaseController $controller, array $list) {
		$objectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
		if ( $parts = $controller->partRepository->findByList($list) ) {
			/** @var \S3b0\EcomConfigCodeGenerator\Domain\Model\Part $part */
			foreach ( $parts as $part ) {
				$part->setActive(TRUE);
				$objectStorage->attach($part);
			}
		}

		return $objectStorage;
	}

	/**
	 * Initialize parts
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage  $parts
	 * @param \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $partGroup
	 * @param array                                         $configuration
	 * @return void
	 */
	public function initializeParts(\TYPO3\CMS\Extbase\Persistence\ObjectStorage &$parts, \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $partGroup, array $configuration) {
		/** @var \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $configurations */
		if ( sizeof($configuration) && ($compatibleParts = $this->configurationRepository->findCompatiblePartsByConfigurationArray($configuration, $partGroup)) ) {
			/** @var \S3b0\EcomSkuGenerator\Domain\Model\Part $part */
			foreach ( $compatibleParts as $part ) {
				$part->setCompatibleToSelection(TRUE);
			}
		} else {
			/** @var \S3b0\EcomSkuGenerator\Domain\Model\Part $part */
			foreach ( $parts as $part ) {
				$part->setCompatibleToSelection(TRUE);
			}
		}
		if ( $this->pricing ) {
			/** @var \S3b0\EcomSkuGenerator\Domain\Model\Part $part */
			foreach ( $parts as $part ) {
				$part->setCurrency($this->currency);
			}
		}
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomConfigCodeGenerator\Domain\Model\Part> $storage
	 * @param array                                                                                         $configuration
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 */
	protected function automaticallySetPartIfNoAlternativeExists(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $storage = NULL, array $configuration) {
		/** @var \S3b0\EcomSkuGenerator\Domain\Model\Part $part */
		$part = $storage->toArray()[0];
		if ( $storage instanceof \TYPO3\CMS\Extbase\Persistence\ObjectStorage && $storage->count() === 1 ) {
			\S3b0\EcomSkuGenerator\Session\ManageConfiguration::removePartGroupFromConfiguration($this, $part->getPartGroup(), $configuration);
			\S3b0\EcomSkuGenerator\Session\ManageConfiguration::addPartToConfiguration($this, $part, $configuration);

			$arguments = $this->request->getArguments();
			ArrayUtility::mergeRecursiveWithOverrule($arguments, [ 'partGroup' => $part->getPartGroup()->getNext() ]);
			$this->forward('index', NULL, NULL, $arguments);
		}
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $configurations
	 * @param array                                               $configuration
	 * @return array
	 */
	protected function getSku($configurations, $configuration) {
		$summaryTableRows = [ ];
		$summaryTableMailRows = [ ];
		$code = [ ];
		$blankCode = [ ];
		if ( $configurations instanceof \Countable && $configurations->count() === 1 ) {
			/** @var \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $partGroup */
			foreach ( $this->contentObject->getSkuGeneratorPartGroups() as $partGroup ) {
				$parts = $configuration[ $partGroup->getUid() ];
				ksort($parts); // Order by sorting
				$partList = [ ];
				foreach ( $parts as $partUid ) {
					/** @var \S3b0\EcomSkuGenerator\Domain\Model\Part $part */
					$part = $this->partRepository->findByUid($partUid);
					$partList[] = $part->getTitle();
				}
				$summaryTableRows[] = ( "
					<td>{$partGroup->getStepIndicator()}</td>
					<td>{$partGroup->getTitle()}</td>
					<td>" . implode(', ', $partList) . "</td>
					<td><a data-part-group=\"{$partGroup->getUid()}\" class=\"generator-part-group-select\"><i class=\"fa fa-edit\"></i></a></td>
				" ) . ( $this->pricing ? "<td style=\"text-align:right\">{$partGroup->getPricing()}</td>" : "" );
					$summaryTableMailRows[] = ( "
					<td>{$partGroup->getTitle()}</td>
					<td>" . implode(', ', $partList) . "</td>
				" );
			}
			ksort($code);      // Order code either incremental or by place in code
			ksort($blankCode); // Order code either incremental or by place in code

			return [
				'title'            => $configurations->getFirst()->getTitle(),
				'code'             => $configurations->getFirst()->getSku(),
				'blankCode'        => $configurations->getFirst()->getSku(),
				'summaryTable'     => $this->sanitize_output('<table><tr>' . implode('</tr><tr>', $summaryTableRows) . '</tr></table>'),
				'summaryTableMail' => $this->sanitize_output('<table><tr>' . implode('</tr><tr>', $summaryTableMailRows) . '</tr></table>')
			];
		}

		return [
			'title'            => '',
			'code'             => '',
			'blankCode'        => '',
			'summaryTable'     => '',
			'summaryTableMail' => ''
		];
	}

	/**
	 * @param \S3b0\EcomConfigCodeGenerator\Domain\Model\Log $log
	 * @param array                                          $configuration
	 */
	protected function addConfigurationToLog(\S3b0\EcomConfigCodeGenerator\Domain\Model\Log &$log, array $configuration) {
		$ipLength = !MathUtility::canBeInterpretedAsInteger($this->settings['log']['ipLength']) || $this->settings['log']['ipLength'] > 4 ? 4 : $this->settings['log']['ipLength'];

		$configurations = $this->configurationRepository->findByConfigurationArray($configuration);
#		$code = $this->getSku($configurations, $configuration);

		/** @var \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $partGroup */
		foreach ( $this->contentObject->getSkuGeneratorPartGroups() as $partGroup ) {
			$parts = $configuration[$partGroup->getUid()];
			ksort($parts); // Order by sorting
			foreach ( $parts as $partUid ) {
				/** @var \S3b0\EcomSkuGenerator\Domain\Model\Part $part */
				$part = $this->partRepository->findByUid($partUid);
				$log->addConfiguredPartSku($part);
			}
		}

		/** Set minimum quantity */
		if ( $log->getQuantity() === 0 ){
			$log->setQuantity(1);
		}
#		/** @var \S3b0\EcomSkuGenerator\Domain\Model\Configuration $currentConfiguration */
#		$currentConfiguration = $configurations->getFirst();
#		$currentConfiguration->setConfigurationPricingNumeric($currentConfiguration->getConfigurationPricingNumeric() * ($log->getQuantity() ?: 1));
		$log->setSessionId($GLOBALS['TSFE']->fe_user->id)
			->setConfiguration($configurations->getFirst()->getSku())
			->maskIpAddress($ipLength)
#			->setPricing($currentConfiguration->getConfigurationPricing())
			->setPid(0);
		if ( $GLOBALS['TSFE']->loginUser ) {
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
	protected function getStandAloneTemplate($templateName, array $variables = []) {
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
	protected function sanitize_output($buffer) {
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