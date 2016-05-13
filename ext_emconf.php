<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "ecom_sku_generator"
 *
 * Auto generated by Extension Builder 2015-10-06
 *
 * Manual updates:
 * Only the data in the array - anything else is removed by next write.
 * "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
	'title' => 'SKU Generator',
	'description' => 'SKU Generator',
	'category' => 'plugin',
	'author' => 'Nicolas Scheidler, Sebastian Iffland',
	'author_email' => 'Nicolas.Scheidler@ecom-ex.com',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => '1',
	'createDirs' => '',
	'clearCacheOnLoad' => 0,
	'version' => '1.0.5',
	'constraints' => [
		'depends' => [
			'cms' => '',
			'typo3' => '7.6.0-7.6.99',
			'php' => '5.6',
			'ecom_toolbox' => '2.0.5',
			'powermail' => '',
            'ecom_product_tools' => '7.6.8',
			'ecom_config_code_generator' => '1.3.5',
		],
		'conflicts' => [
		],
		'suggests' => [
		],
	],
];