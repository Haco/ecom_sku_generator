<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

$translate = 'LLL:EXT:ecom_config_code_generator/Resources/Private/Language/locallang_db.xlf:';

return [
    'ctrl' => [
        'adminOnly' => true,
        'title' => 'LLL:EXT:ecom_sku_generator/Resources/Private/Language/locallang_db.xlf:tx_ecomskugenerator_domain_model_dependentnote',
        'label' => 'note',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'versioningWS' => 2,
        'versioning_followPages' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'hideTable' => true,
        'enablecolumns' => [
            'disabled' => 'hidden'
        ],
        'searchFields' => 'note,note_wrap,use_logical_and,dependent_parts',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('sys_note') . 'ext_icon.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, note, note_wrap, use_logical_and, dependent_parts'
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, note;;2;wizards[t3editorHtml]']
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
        '2' => [
            'showitem' => 'note_wrap, --linebreak--, dependent_parts, --linebreak--, use_logical_and, part_group',
            'canNotCollapse' => true
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
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1],
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0]
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
                    ['', 0]
                ],
                'foreign_table' => 'tx_ecomskugenerator_domain_model_dependentnote',
                'foreign_table_where' => 'AND tx_ecomskugenerator_domain_model_dependentnote.pid=###CURRENT_PID### AND tx_ecomskugenerator_domain_model_dependentnote.part_group=(SELECT l10n_parent FROM tx_ecomskugenerator_domain_model_partgroup WHERE uid=###REC_FIELD_part_group###) AND tx_ecomskugenerator_domain_model_dependentnote.sys_language_uid IN (-1,0)',
            ]
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ]
        ],

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
            ]
        ],

        'note' => [
            'l10n_mode' => 'prefixLangTitle',
            'exclude' => 0,
            'label' => "{$translate}tx_ecomconfigcodegenerator_domain_model_dependentnote.note",
            'config' => [
                'type' => 'text',
                'cols' => 100,
                'rows' => 10,
                'eval' => 'trim,required',
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
        'note_wrap' => [
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => "{$translate}tx_ecomconfigcodegenerator_domain_model_dependentnote.note_wrap",
            'config' => [
                'type' => 'select',
                'items' => [
                    ["{$translate}select.empty", 0],
                    ["{$translate}tx_ecomconfigcodegenerator_domain_model_dependentnote.note_wrap.success", 1],
                    ["{$translate}tx_ecomconfigcodegenerator_domain_model_dependentnote.note_wrap.info", 2],
                    ["{$translate}tx_ecomconfigcodegenerator_domain_model_dependentnote.note_wrap.warning", 3],
                    ["{$translate}tx_ecomconfigcodegenerator_domain_model_dependentnote.note_wrap.danger", 4]
                ]
            ]
        ],
        'use_logical_and' => [
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => '',
            'config' => [
                'type' => 'check',
                'items' => [
                    ["{$translate}tx_ecomconfigcodegenerator_domain_model_dependentnote.use_logical_and"]
                ]
            ]
        ],
        'dependent_parts' => [
            'displayCond' => 'REC:NEW:FALSE',
            'l10n_mode' => 'exclude',
            'exclude' => 1,
            'label' => "{$translate}tx_ecomconfigcodegenerator_domain_model_dependentnote.dependent_parts",
            'config' => [
                'type' => 'select',
                'foreign_table' => 'tx_ecomskugenerator_domain_model_part',
                'foreign_table_where' => ('
					AND tx_ecomskugenerator_domain_model_part.pid=###CURRENT_PID###
					AND NOT tx_ecomskugenerator_domain_model_part.deleted
					AND tx_ecomskugenerator_domain_model_part.sys_language_uid IN (-1,0)
					AND ( SELECT sorting FROM tx_ecomskugenerator_domain_model_partgroup WHERE tx_ecomskugenerator_domain_model_partgroup.uid=tx_ecomskugenerator_domain_model_part.part_group ) < ( SELECT sorting FROM tx_ecomskugenerator_domain_model_partgroup WHERE tx_ecomskugenerator_domain_model_partgroup.uid=###REC_FIELD_part_group### )
					ORDER BY tx_ecomskugenerator_domain_model_part.part_group, tx_ecomskugenerator_domain_model_part.title
				'),
                'MM' => 'tx_ecomskugenerator_dependentnote_part_mm',
                'itemsProcFunc' => \S3b0\EcomSkuGenerator\User\ModifyTCA\ModifyTCA::class . '->itemsProcFuncEcomSkuGeneratorDomainModelDependentNoteDependentParts',
                'size' => 10,
                'autoSizeMax' => 30,
                'minitems' => 1,
                'maxitems' => 9999,
                'multiple' => 0,
                'renderMode' => 'checkbox',
                'disableNoMatchingValueElement' => 1
            ]
        ],

        'part_group' => [
            'l10n_mode' => 'exclude',
            'label' => "{$translate}tx_ecomskugenerator_domain_model_partgroup",
            'config' => [
                'type' => 'select',
                'readOnly' => 1,
                'foreign_table' => 'tx_ecomskugenerator_domain_model_partgroup'
            ]
        ]
    ]
];
