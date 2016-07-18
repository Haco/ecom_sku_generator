<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'S3b0.' . $_EXTKEY,
    'Generator',
    ['Generator' => 'index, currencySelect, setCurrency, reset', 'Log' => 'new, create, confirmation'],
    // non-cacheable actions
    ['Generator' => 'index, setCurrency, reset', 'Log' => 'create']
);

$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['EcomSkuGenerator'] = 'EXT:ecom_sku_generator/Classes/AjaxDispatcher.php';