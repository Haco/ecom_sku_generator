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
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Class AjaxRequestController
 * @package S3b0\EcomSkuGenerator\Controller
 */
class AjaxRequestController extends \S3b0\EcomSkuGenerator\Controller\GeneratorController {

	/**
	 * @var \TYPO3\CMS\Extbase\Mvc\View\JsonView $view
	 */
	protected $view;

	/**
	 * @var string
	 */
	protected $defaultViewObjectName = \TYPO3\CMS\Extbase\Mvc\View\JsonView::class;

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
		global $TYPO3_CONF_VARS;
		/** !!! IMPORTANT TO MAKE JSON WORK !!! */
		$TYPO3_CONF_VARS['FE']['debug'] = '0';
		$this->contentObject = $this->contentRepository->findByUid($this->request->getArgument('cObj'));
		parent::initializeAction();
	}

	/**
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
	 */
	public function initializeIndexAction() {
		if ( $this->request->hasArgument('partGroup') && MathUtility::canBeInterpretedAsInteger($this->request->getArgument('partGroup')) && !$this->request->getArgument('partGroup') instanceof \S3b0\EcomConfigCodeGenerator\Domain\Model\PartGroup )
			$this->request->setArgument('partGroup', $this->partGroupRepository->findByUid($this->request->getArgument('partGroup')));
	}

	/**
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
	 */
	public function initializeUpdatePartAction() {
		if ( $this->request->hasArgument('part') && MathUtility::canBeInterpretedAsInteger($this->request->getArgument('part')) && !$this->request->getArgument('part') instanceof \S3b0\EcomConfigCodeGenerator\Domain\Model\Part )
			$this->request->setArgument('part', $this->partRepository->findByUid($this->request->getArgument('part')));
	}

	/**
	 * action updatePart
	 *
	 * @param \S3b0\EcomSkuGenerator\Domain\Model\Part|NULL $part
	 * @param boolean                                       $unset
	 * @return void
	 */
	public function updatePartAction(\S3b0\EcomSkuGenerator\Domain\Model\Part $part = NULL, $unset = FALSE) {
        $configuration = $this->feSession->get('config') ?: [];
		// Manage Session data
		if ( $unset === FALSE ) {
			\S3b0\EcomSkuGenerator\Session\ManageConfiguration::addPartToConfiguration($this, $part, $configuration);
		} else {
			\S3b0\EcomSkuGenerator\Session\ManageConfiguration::removePartFromConfiguration($this, $part, $configuration);
		}

		$arguments = $part->getPartGroup()->isMultipleSelectable() ? [ $part->getPartGroup() ] : [ ];
		$data = parent::getIndexActionData($arguments);
		$data['part'] = $part;
		$data['multiple'] = $part->getPartGroup()->isMultipleSelectable();

		$this->view->assign('value', $data);
	}

	/**
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
	 */
	public function initializeShowHintAction() {
		if ( $this->request->hasArgument('part') && MathUtility::canBeInterpretedAsInteger($this->request->getArgument('part')) && !$this->request->getArgument('part') instanceof \S3b0\EcomSkuGenerator\Domain\Model\Part)
			$this->request->setArgument('part', $this->partRepository->findByUid($this->request->getArgument('part')));
	}

	/**
	 * Shows bootstrap hint modal for parts
	 *
	 * @param \S3b0\EcomSkuGenerator\Domain\Model\Part|null $part
	 * @return void
	 */
	public function showHintAction(\S3b0\EcomSkuGenerator\Domain\Model\Part $part = NULL) {
        $title = $part->getTitle();
        $hint = $part->getHint();

        $this->view->assign('value', [
            'partTitle' => $this->sanitize_output($title),
            'partHint' => $this->sanitize_output($hint)
        ]);
	}


	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $parts
	 * @return string
	 */
	public function getPartSelectorHTML(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $parts) {
		/** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
		$view = $this->objectManager->get(\TYPO3\CMS\Fluid\View\StandaloneView::class);

		$extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
		$partialRootPaths = [ ];
		$templatePathAndFilename = '';
		if ( !$extbaseFrameworkConfiguration['view']['partialRootPath'] ) {
			foreach ( (array) $extbaseFrameworkConfiguration['view']['partialRootPaths'] as $path ) {
				$partialRootPaths[] = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($path);
				if ( file_exists(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($path) . 'Part/Selector.html') ) {
					$templatePathAndFilename = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($path) . 'Part/Selector.html';
				}
			}
		} else {
			$partialRootPaths[] = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['partialRootPath']);
			$templatePathAndFilename = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['partialRootPath']) . 'Part/Selector.html';
		}
		$view->setTemplatePathAndFilename($templatePathAndFilename);
		$view->setPartialRootPaths($partialRootPaths);
		$view->assignMultiple([
			'parts' => $parts,
			'pricingEnabled' => $this->pricing
		]);
		$view->setFormat('html');

		return $this->sanitize_output($view->render());
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\S3b0\EcomConfigCodeGenerator\Domain\Model\PartGroup> $partGroups
	 * @return string
	 */
	public function getPartGroupSelectorHTML(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $partGroups) {
		/** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
		$view = $this->objectManager->get(\TYPO3\CMS\Fluid\View\StandaloneView::class);

		$extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
		$partialRootPaths = [ ];
		$templatePathAndFilename = '';
		if ( !$extbaseFrameworkConfiguration['view']['partialRootPath'] ) {
			foreach ( (array) $extbaseFrameworkConfiguration['view']['partialRootPaths'] as $path ) {
				$partialRootPaths[] = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($path);
				if ( file_exists(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($path) . 'PartGroup/Selector.html') ) {
					$templatePathAndFilename = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($path) . 'PartGroup/Selector.html';
				}
			}
		} else {
			$partialRootPaths[] = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['partialRootPath']);
			$templatePathAndFilename = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($extbaseFrameworkConfiguration['view']['partialRootPath']) . 'PartGroup/Selector.html';
		}
		$view->setTemplatePathAndFilename($templatePathAndFilename);
		$view->setPartialRootPaths($partialRootPaths);
		$view->assign('partGroups', $partGroups);
		$view->setFormat('html');

		return $this->sanitize_output($view->render());
	}

}
