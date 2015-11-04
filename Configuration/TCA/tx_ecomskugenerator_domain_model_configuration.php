<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

/** @var \TYPO3\CMS\Core\Database\DatabaseConnection $db */
$db = $GLOBALS['TYPO3_DB'];
$maxItemsPricing = $db->exec_SELECTcountRows('*', 'tx_ecomconfigcodegenerator_domain_model_currency');

return [
	'ctrl' => [
		'adminOnly' => TRUE,
		'title'	=> 'LLL:EXT:ecom_sku_generator/Resources/Private/Language/locallang_db.xlf:tx_ecomskugenerator_domain_model_configuration',
		'label' => 'sku',
		'label_alt' => 'title',
		'label_alt_force' => TRUE,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'delete' => 'deleted',
		'enablecolumns' => [
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime'
		],
		'searchFields' => 'title,sku,parts,pricing,',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('ecom_sku_generator') . 'Resources/Public/Icons/tx_ecomskugenerator_domain_model_configuration.gif'
	],
	'interface' => [
		'showRecordFieldList' => 'hidden, title, sku, parts, pricing',
	],
	'types' => [
		'1' => [ 'showitem' => 'hidden;;1;;1-1-1, title, sku, parts, --div--;LLL:EXT:ecom_config_code_generator/Resources/Private/Language/locallang_db.xlf:tabs.pricing, pricing, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime' ],
	],
	'palettes' => [
		'1' => [ 'showitem' => '' ],
	],
	'columns' => [

		't3ver_label' => [
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			]
		],

		'hidden' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => [
				'type' => 'check',
			],
		],
		'starttime' => [
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => [
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => [
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				],
			],
		],
		'endtime' => [
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => [
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => [
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				],
			],
		],

		'title' => [
			'exclude' => 0,
			'label' => 'LLL:EXT:ecom_sku_generator/Resources/Private/Language/locallang_db.xlf:tx_ecomskugenerator_domain_model_configuration.title',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			],
		],
		'sku' => [
			'exclude' => 0,
			'label' => 'LLL:EXT:ecom_sku_generator/Resources/Private/Language/locallang_db.xlf:tx_ecomskugenerator_domain_model_configuration.sku',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required,uniqueInPid'
			],
		],
		'parts' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:ecom_sku_generator/Resources/Private/Language/locallang_db.xlf:tx_ecomskugenerator_domain_model_configuration.parts',
			'config' => [
				'type' => 'select',
				'form_type' => 'user',
				'renderType' => 'user',
				'userFunc' => 'S3b0\\EcomSkuGenerator\\User\\ModifyTCA\\ModifyTCA->selectPartsUserField',
				'foreign_table' => 'tx_ecomskugenerator_domain_model_part',
				/*'foreign_table_where' => ('
					AND NOT tx_ecomskugenerator_domain_model_part.deleted
					AND tx_ecomskugenerator_domain_model_part.sys_language_uid IN (-1,0)
					AND (
						SELECT content FROM tx_ecomskugenerator_domain_model_partgroup WHERE tx_ecomskugenerator_domain_model_partgroup.uid=tx_ecomskugenerator_domain_model_part.part_group
					)=###REC_FIELD_content###
					ORDER BY tx_ecomskugenerator_domain_model_part.part_group, tx_ecomskugenerator_domain_model_part.sorting
				'),*/
				'MM' => 'tx_ecomskugenerator_configuration_part_mm',
				'size' => 10,
				'autoSizeMax' => 30,
				'maxitems' => 9999,
				'multiple' => 0,
				'renderMode' => 'checkbox',
				'disableNoMatchingValueElement' => 1
			],
		],
		'pricing' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:ecom_sku_generator/Resources/Private/Language/locallang_db.xlf:tx_ecomskugenerator_domain_model_configuration.pricing',
			'config' => [
				'type' => 'inline',
				'foreign_table' => 'tx_ecomconfigcodegenerator_domain_model_price',
				'foreign_field' => 'sku_configuration',
				'maxitems'      => $maxItemsPricing,
				'appearance' => [
					'collapseAll' => 0,
					'levelLinksPosition' => 'top',
					'showSynchronizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'showAllLocalizationLink' => 1
				],
			],

		],

		'content' => [
			'config' => [
				'type' => 'passthrough'
			]
		]
	],
];
