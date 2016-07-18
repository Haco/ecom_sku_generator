<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

$tempColumns = [
    'sku_configuration' => [
        'displayCond' => [
            'AND' => [
                'REC:NEW:FALSE',
                'FIELD:configuration:=:0',
                'FIELD:part:=:0',
                'FIELD:sku_part:=:0',
                'FIELD:content:=:0'
            ]
        ],
        'label' => 'LLL:EXT:ecom_config_code_generator/Resources/Private/Language/locallang_db.xlf:tx_ecomconfigcodegenerator_domain_model_configuration',
        'config' => [
            'type' => 'select',
            'readOnly' => 1,
            'foreign_table' => 'tx_ecomskugenerator_domain_model_configuration',
            'suppress_icons' => 1
        ]
    ],
    'sku_part' => [
        'displayCond' => [
            'AND' => [
                'REC:NEW:FALSE',
                'FIELD:configuration:=:0',
                'FIELD:part:=:0',
                'FIELD:sku_configuration:=:0',
                'FIELD:content:=:0'
            ]
        ],
        'label' => 'LLL:EXT:ecom_config_code_generator/Resources/Private/Language/locallang_db.xlf:tx_ecomconfigcodegenerator_domain_model_part',
        'config' => [
            'type' => 'select',
            'readOnly' => 1,
            'foreign_table' => 'tx_ecomskugenerator_domain_model_part',
            'suppress_icons' => 1
        ]
    ],
    'content' => [
        'displayCond' => [
            'AND' => [
                'REC:NEW:FALSE',
                'FIELD:configuration:=:0',
                'FIELD:part:=:0',
                'FIELD:sku_configuration:=:0',
                'FIELD:sku_part:=:0'
            ]
        ],
        'label' => 'LLL:EXT:ecom_config_code_generator/Resources/Private/Language/locallang_db.xlf:tx_ecomconfigcodegenerator_domain_model_content',
        'config' => [
            'type' => 'select',
            'readOnly' => 1,
            'foreign_table' => 'tt_content',
            'suppress_icons' => 1
        ]
    ]
];

// Add fields to TCA
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_ecomconfigcodegenerator_domain_model_price', $tempColumns, true);
// Add fields to palette
$GLOBALS['TCA']['tx_ecomconfigcodegenerator_domain_model_price']['palettes']['2']['showitem'] .= ', sku_configuration, sku_part, content';

// Extend display conditions
$GLOBALS['TCA']['tx_ecomconfigcodegenerator_domain_model_price']['columns']['configuration']['displayCond'] = [
    'AND' => [
        'REC:NEW:FALSE',
        'FIELD:part:=:0',
        'FIELD:sku_configuration:=:0',
        'FIELD:sku_part:=:0',
        'FIELD:content:=:0'
    ]
];
$GLOBALS['TCA']['tx_ecomconfigcodegenerator_domain_model_price']['columns']['part']['displayCond'] = [
    'AND' => [
        'REC:NEW:FALSE',
        'FIELD:configuration:=:0',
        'FIELD:sku_configuration:=:0',
        'FIELD:sku_part:=:0',
        'FIELD:content:=:0'
    ]
];