<?php
namespace S3b0\EcomSkuGenerator;

/***************************************************************
 * Copyright notice
 *
 * 2015 Sebastian Iffland <Sebastian.Iffland@ecom-ex.com>, ecom instruments GmbH
 * All rights reserved
 *
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Utility as CoreUtility;

/**
 * Class AjaxDispatcher
 *
 * @author Sebastian Iffland <Sebastian.Iffland@ecom-ex.com>, ecom instruments GmbH
 */
class AjaxDispatcher extends \Ecom\EcomToolbox\Utility\AjaxDispatcher
{

    protected $defaultVendorName = 'S3b0';
    protected $defaultExtensionName = 'EcomSkuGenerator';
    protected $defaultPluginName = 'ecomskugenerator_generator';
    protected $defaultControllerName = 'AjaxRequest';
    protected $defaultActionName = 'index';
    protected $pageType = 1444800649;

}

global $TYPO3_CONF_VARS;

/** !!! IMPORTANT TO MAKE JSON WORK !!! */
$TYPO3_CONF_VARS['FE']['debug'] = '0';

/** @var \S3b0\EcomSkuGenerator\AjaxDispatcher $dispatcher */
$dispatcher = CoreUtility\GeneralUtility::makeInstance(AjaxDispatcher::class);

// ATTENTION! Dispatcher first needs to be initialized here!!!
echo $dispatcher
    ->init($TYPO3_CONF_VARS)
    ->dispatch();

