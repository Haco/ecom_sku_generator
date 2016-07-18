<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $_EXTKEY,
    'Generator',
    'SKU Generator'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Resources/Private/TypoScript', 'SKU Generator');

// Allow tables on standard pages
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_ecomskugenerator_domain_model_configuration');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_ecomskugenerator_domain_model_partgroup');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_ecomskugenerator_domain_model_part');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_ecomskugenerator_domain_model_dependentnote');

// Add context sensitive help
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_ecomskugenerator_domain_model_configuration',
    'EXT:ecom_sku_generator/Resources/Private/Language/locallang_csh_tx_ecomskugenerator_domain_model_configuration.xlf');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_ecomskugenerator_domain_model_partgroup',
    'EXT:ecom_sku_generator/Resources/Private/Language/locallang_csh_tx_ecomskugenerator_domain_model_partgroup.xlf');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_ecomskugenerator_domain_model_part',
    'EXT:ecom_sku_generator/Resources/Private/Language/locallang_csh_tx_ecomskugenerator_domain_model_part.xlf');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_ecomskugenerator_domain_model_dependentnote',
    'EXT:ecom_sku_generator/Resources/Private/Language/locallang_csh_tx_ecomskugenerator_domain_model_dependentnote.xlf');

// Hook backend
#$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/template.php']['preHeaderRenderHook'][] = 'S3b0\\EcomSkuGenerator\\Hooks\\PreHeaderRenderHook->main';