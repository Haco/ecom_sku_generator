<?php
	/**
	 * Created by PhpStorm.
	 * User: S3b0
	 * Date: 07/10/15
	 * Time: 9:41 AM
	 */

	namespace S3b0\EcomSkuGenerator\Hooks;
	use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

	/**
	 * Class PreHeaderRenderHook
	 * @package S3b0\EcomSkuGenerator\Hooks
	 */
	class PreHeaderRenderHook {

		/**
		 * @param array                                        $arg
		 * @param \TYPO3\CMS\Backend\Template\DocumentTemplate $template
		 */
		function main($arg, $template) {
			/** @var $pagerenderer \TYPO3\CMS\Core\Page\PageRenderer */
			$pagerenderer = $arg['pageRenderer'];
			$pagerenderer->addJsFile($GLOBALS['BACK_PATH'] . ExtensionManagementUtility::extRelPath('ecom_sku_generator') . 'Resources/Public/Backend/JavaScript/main.js');
		}

	}