<?php
$translate = "LLL:EXT:ecom_sku_generator/Resources/Private/Language/locallang_db.xlf:";

/** @var \TYPO3\CMS\Core\Database\DatabaseConnection $db */
$db = $GLOBALS['TYPO3_DB'];
$maxItemsPricing = $db->exec_SELECTcountRows('*', 'tx_ecomconfigcodegenerator_domain_model_currency');

$tempColumns = [
    'sku_generator_part_groups' => [
        'exclude' => 1,
        'label' => "{$translate}tx_ecomskugenerator_domain_model_content.sku_generator_part_groups",
        'config' => [
            'type' => 'inline',
            'foreign_table' => 'tx_ecomskugenerator_domain_model_partgroup',
            'foreign_field' => 'content',
            'maxitems' => 9999,
            'appearance' => [
                'collapseAll' => 1,
                'expandSingle' => 1,
                'levelLinksPosition' => 'bottom',
                'showAllLocalizationLink' => 1,
                'showPossibleLocalizationRecords' => 1,
                'showSynchronizationLink' => 1,
                'useSortable' => 1
            ],
            'behaviour' => [
                'localizationMode' => 'select',
                'localizeChildrenAtParentLocalization' => 1,
                'disableMovingChildrenWithParent' => 0,
                'enableCascadingDelete' => 1
            ]
        ]
    ],
    'sku_generator_configurations' => [
        'l10n_mode' => 'exclude',
        'exclude' => 1,
        'label' => "{$translate}tx_ecomskugenerator_domain_model_content.sku_generator_configurations",
        'config' => [
            'type' => 'inline',
            'foreign_table' => 'tx_ecomskugenerator_domain_model_configuration',
            'foreign_field' => 'content',
            'maxitems' => 9999,
            'appearance' => [
                'collapseAll' => 1,
                'expandSingle' => 1,
                'levelLinksPosition' => 'bottom',
                'showAllLocalizationLink' => 0,
                'showPossibleLocalizationRecords' => 0,
                'showSynchronizationLink' => 0,
                'useSortable' => 1
            ],
            'behaviour' => [
                'localizationMode' => 'keep',
                'localizeChildrenAtParentLocalization' => 0,
                'disableMovingChildrenWithParent' => 0,
                'enableCascadingDelete' => 0
            ]
        ]
    ],
    'sku_generator_pricing' => [
        'l10n_mode' => 'exclude',
        'displayCond' => 'FIELD:sku_generator_pricing_enabled:REQ:TRUE',
        'exclude' => 1,
        'label' => "{$translate}tx_ecomskugenerator_domain_model_content.sku_generator_pricing",
        'config' => [
            'type' => 'inline',
            'foreign_table' => 'tx_ecomconfigcodegenerator_domain_model_price',
            'foreign_field' => 'content',
            'maxitems' => $maxItemsPricing,
            'appearance' => [
                'collapseAll' => 1,
                'expandSingle' => 1,
                'levelLinksPosition' => 'bottom',
                'showAllLocalizationLink' => 0,
                'showPossibleLocalizationRecords' => 0,
                'showSynchronizationLink' => 0
            ],
            'behaviour' => [
                'localizationMode' => 'keep',
                'localizeChildrenAtParentLocalization' => 0,
                'disableMovingChildrenWithParent' => 0,
                'enableCascadingDelete' => 0
            ]
        ]
    ],
    'sku_generator_pricing_enabled' => [
        'l10n_mode' => 'exclude',
        'exclude' => 1,
        'label' => "{$translate}tx_ecomskugenerator_domain_model_content.sku_generator_pricing_enabled",
        'config' => [
            'type' => 'check',
        ]
    ]
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $tempColumns, true);

$GLOBALS['TCA']['tt_content']['ctrl']['requestUpdate'] .= ',sku_generator_pricing_enabled';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['ecomskugenerator_generator'] = ("
	sku_generator_configurations, sku_generator_part_groups, sku_generator_pricing_enabled, sku_generator_pricing,
	--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.extended, bodytext;SKU Instructions;;richtext:rte_transform[flag=rte_enabled|mode=ts_css], rte_enabled
");