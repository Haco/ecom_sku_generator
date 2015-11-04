<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$tempColumns = [
	'configured_parts_sku' => [
		'displayCond' => 'FIELD:configured_parts:REQ:FALSE',
		'exclude' => 1,
		'label' => 'LLL:EXT:ecom_config_code_generator/Resources/Private/Language/locallang_db.xlf:tx_ecomconfigcodegenerator_domain_model_log.configured_parts',
		'config' => [
			'type' => 'select',
			'foreign_table' => 'tx_ecomskugenerator_domain_model_part',
			'foreign_table_where' => 'ORDER BY tx_ecomskugenerator_domain_model_part.part_group, tx_ecomskugenerator_domain_model_part.sorting',
			'size' => 30,
			'autoSizeMax' => 30,
			'maxitems' => 9999,
			'readOnly' => 1
		]
	]

];

// Add fields to TCA
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_ecomconfigcodegenerator_domain_model_log', $tempColumns, TRUE);
// Add fields to types
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tx_ecomconfigcodegenerator_domain_model_log', 'configured_parts_sku', '', 'after:quantity');

// Extend display conditions
$GLOBALS['TCA']['tx_ecomconfigcodegenerator_domain_model_log']['columns']['configured_parts']['displayCond'] = 'FIELD:configured_parts_sku:REQ:FALSE';