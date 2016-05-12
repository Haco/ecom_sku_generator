<?php
if ( !defined ('TYPO3_MODE') ) {
	die ('Access denied.');
}

$translate = 'LLL:EXT:ecom_config_code_generator/Resources/Private/Language/locallang_db.xlf:';

  // Set settings item array
$settings = [
	[ "{$translate}tx_ecomconfigcodegenerator_domain_model_partgroup.settings.general.enableMultipleSelect" ]
];

return [
	'ctrl' => [
		'adminOnly' => TRUE,
		'title'	=> 'LLL:EXT:ecom_sku_generator/Resources/Private/Language/locallang_db.xlf:tx_ecomskugenerator_domain_model_partgroup',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => [
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group'
		],
		'searchFields' => 'title,icon,prompt,prompt_wrap,parts,dependent_notes',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('ecom_config_code_generator') . 'Resources/Public/Icons/tx_ecomconfigcodegenerator_domain_model_partgroup.png'
	],
	'interface' => [
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, icon, prompt, prompt_wrap, settings, parts, dependent_notes'
	],
	'types' => [
		'1' => [ 'showitem' => "sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, title, prompt;;3;wizards[t3editorHtml], settings, content, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.extended, dependent_notes, --div--;{$translate}tabs.parts, parts, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.appearance, icon, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, --palette--;LLL:EXT:cms/locallang_tca.xlf:pages.palettes.access;access" ]
	],
	'palettes' => [
		'1' => [
			'showitem' => ''
		],
		'2' => [
			'showitem' => 'prompt_wrap'
		],
		'access' => [
			'showitem' => 'starttime;LLL:EXT:cms/locallang_tca.xlf:pages.starttime_formlabel, endtime;LLL:EXT:cms/locallang_tca.xlf:pages.endtime_formlabel, --linebreak--, fe_group;LLL:EXT:cms/locallang_tca.xlf:pages.fe_group_formlabel',
			'canNotCollapse' => TRUE
		]
	],
	'columns' => [

		'sys_language_uid' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => [
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => [
					[ 'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1 ],
					[ 'LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0 ]
				]
			]
		],
		'l10n_parent' => [
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => [
				'type' => 'select',
				'items' => [
					[ '', 0 ],
				],
				'foreign_table' => 'tx_ecomskugenerator_domain_model_partgroup',
				'foreign_table_where' => 'AND tx_ecomskugenerator_domain_model_partgroup.pid=###CURRENT_PID### AND tx_ecomskugenerator_domain_model_partgroup.sys_language_uid IN (-1,0)'
			]
		],
		'l10n_diffsource' => [
			'config' => [
				'type' => 'passthrough'
			]
		],

		't3ver_label' => [
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'max' => 255
			]
		],

		'hidden' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => [
				'type' => 'check'
			]
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
				]
			]
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
				]
			]
		],
		'fe_group' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.fe_group',
			'config' => [
				'type' => 'select',
				'size' => 7,
				'maxitems' => 20,
				'items' => [
					[
						'LLL:EXT:lang/locallang_general.xlf:LGL.hide_at_login',
						-1
					],
					[
						'LLL:EXT:lang/locallang_general.xlf:LGL.any_login',
						-2
					],
					[
						'LLL:EXT:lang/locallang_general.xlf:LGL.usergroups',
						'--div--'
					]
				],
				'exclusiveKeys' => '-1,-2',
				'foreign_table' => 'fe_groups',
				'foreign_table_where' => 'ORDER BY fe_groups.title'
			]
		],

		'title' => [
			'l10n_mode' => 'prefixLangTitle',
			'exclude' => 0,
			'label' => "{$translate}tx_ecomconfigcodegenerator_domain_model_partgroup.title",
			'config' => [
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			]
		],
		'icon' => [
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => "{$translate}tx_ecomconfigcodegenerator_domain_model_partgroup.icon",
			'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
				'icon',
				[
					'maxitems' => 1,
					'appearance' => [
						'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference',
						'enabledControls' => [
							'localize' => 0
						]
					],
					'behaviour' => [
						'localizeChildrenAtParentLocalization' => 0
					],
					'foreign_types' => [
						\TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
							'showitem' => '--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,--palette--;;filePalette'
						],
					],
					'filter' => [
						'0' => [
							'parameters' => [
								'allowedFileExtensions' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
							]
						]
					]
				],
				$GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
			)
		],
		'prompt' => [
			'l10n_mode' => 'prefixLangTitle',
			'exclude' => 0,
			'label' => "{$translate}tx_ecomconfigcodegenerator_domain_model_partgroup.prompt",
			'config' => [
				'type' => 'text',
				'cols' => 100,
				'rows' => 10,
				'eval' => 'trim',
				'wizards' => [
					't3editorHtml' => [
						'enableByTypeConfig' => 1,
						'type' => 'userFunc',
						'userFunc' => 'TYPO3\\CMS\\T3editor\\FormWizard->main',
						'params' => [
							'format' => 'html'
						]
					]
				]
			]
		],
		'prompt_wrap' => [
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => "{$translate}tx_ecomconfigcodegenerator_domain_model_partgroup.prompt_wrap",
			'config' => [
				'type' => 'select',
				'items' => [
					[ "{$translate}select.empty", 0 ],
					[ "{$translate}tx_ecomconfigcodegenerator_domain_model_partgroup.prompt_wrap.success", 1 ],
					[ "{$translate}tx_ecomconfigcodegenerator_domain_model_partgroup.prompt_wrap.info", 2 ],
					[ "{$translate}tx_ecomconfigcodegenerator_domain_model_partgroup.prompt_wrap.warning", 3 ],
					[ "{$translate}tx_ecomconfigcodegenerator_domain_model_partgroup.prompt_wrap.danger", 4 ]
				]
			]
		],
		'settings' => [
			'l10n_mode' => 'exclude',
			'exclude' => 1,
			'label' => "{$translate}tx_ecomconfigcodegenerator_domain_model_partgroup.settings",
			'config' => [
				'type' => 'check',
				'items' => $settings
			]
		],
		'parts' => [
			'exclude' => 1,
			'label' => "{$translate}tx_ecomconfigcodegenerator_domain_model_partgroup.parts",
			'config' => [
				'type' => 'inline',
				'foreign_table' => 'tx_ecomskugenerator_domain_model_part',
				'foreign_field' => 'part_group',
				'foreign_sortby' => 'sorting',
				'maxitems'      => 9999,
				'appearance' => [
					'collapseAll' => 1,
					'expandSingle' => 1,
					'newRecordLinkAddTitle' => 0,
					'newRecordLinkTitle' => "{$translate}tx_ecomconfigcodegenerator_domain_model_partgroup.parts.inlineElementAddTitle",
					'levelLinksPosition' => 'bottom',
					'showAllLocalizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'showSynchronizationLink' => 1,
					'useSortable' => 1
				],
				'behaviour' => [
					'localizationMode' => 'select',
					'localizeChildrenAtParentLocalization' => 0,
					'disableMovingChildrenWithParent' => 0,
					'enableCascadingDelete' => 1
				]
			]
		],
		'dependent_notes' => [
			'exclude' => 1,
			'label' => "{$translate}tx_ecomconfigcodegenerator_domain_model_partgroup.dependent_notes",
			'config' => [
				'type' => 'inline',
				'foreign_table' => 'tx_ecomskugenerator_domain_model_dependentnote',
				'foreign_field' => 'part_group',
				'maxitems'      => 9999,
				'appearance' => [
					'collapseAll' => 1,
					'expandSingle' => 1,
					'newRecordLinkAddTitle' => 0,
					'newRecordLinkTitle' => "{$translate}tx_ecomconfigcodegenerator_domain_model_partgroup.dependent_notes.inlineElementAddTitle",
					'levelLinksPosition' => 'bottom',
					'showAllLocalizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'showSynchronizationLink' => 1
				],
				'behaviour' => [
					'localizationMode' => 'select',
					'localizeChildrenAtParentLocalization' => 0,
					'disableMovingChildrenWithParent' => 0,
					'enableCascadingDelete' => 1
				]
			]
		],

		'content' => [
			'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:ecom_sku_generator/Resources/Private/Language/locallang_db.xlf:tx_ecomskugenerator_domain_model_content',
			'config' => [
				'type' => 'select',
				'readOnly' => 1,
				'foreign_table' => 'tt_content',
                'foreign_table_where' => 'AND tt_content.pid=###CURRENT_PID###'
			]
		]

	]
];
