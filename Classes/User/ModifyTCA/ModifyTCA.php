<?php

namespace S3b0\EcomSkuGenerator\User\ModifyTCA;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Sebastian Iffland <Sebastian.Iffland@ecom-ex.com>, ecom instruments GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use S3b0\EcomConfigCodeGenerator\Setup;
use TYPO3\CMS\Backend\Utility as BackendUtility;
use TYPO3\CMS\Core\Utility as CoreUtility;

/**
 * Class ModifyTCA
 * @package S3b0\EcomSkuGenerator\User\ModifyTCA
 */
class ModifyTCA {

	/**
	 * @param array $PA
	 * @param \TYPO3\CMS\Backend\Form\FormEngine|\TYPO3\CMS\Backend\Form\Element\UserElement $pObj
	 *
	 * @internal
	 * @return string
	 */
	public function selectPartsUserField(array &$PA, $pObj) {
        $configurationUid = $PA['row']['uid'];

		if ( !$PA['row']['content'] )
			return is_numeric($PA['row']['uid']) ? 'Not linked to any content object! Contact your system administrator for further assistance.' : 'Save to update mask!';
		$db = $this->getDatabaseConnection();
		$addWhere = ("
			NOT tx_ecomskugenerator_domain_model_partgroup.deleted
			AND tx_ecomskugenerator_domain_model_partgroup.sys_language_uid IN (-1,0)
			AND tx_ecomskugenerator_domain_model_partgroup.content={$PA['row']['content']}
		");
		$orderBy = 'tx_ecomskugenerator_domain_model_partgroup.sorting';
		$partGroups = $db->exec_SELECTgetRows('*', 'tx_ecomskugenerator_domain_model_partgroup', $addWhere, '', $orderBy);
		if ( !sizeof($partGroups) )
			return 'Please add some part groups to be selected first!';
		$parts = [];
		foreach ( $partGroups as $k => $partGroup ) {
			$addWhere = ("
				NOT tx_ecomskugenerator_domain_model_part.deleted
				AND tx_ecomskugenerator_domain_model_part.sys_language_uid IN (-1,0)
				AND tx_ecomskugenerator_domain_model_part.part_group={$partGroup['uid']}
			");
			$orderBy = 'tx_ecomskugenerator_domain_model_part.sorting';
			$parts[$partGroup['uid']] = $db->exec_SELECTgetRows('*', 'tx_ecomskugenerator_domain_model_part', $addWhere, '', $orderBy);
			if ( !is_array($parts[$partGroup['uid']]) || sizeof($parts[$partGroup['uid']]) === 0 ) {
				unset($partGroups[$k]);
			}
		}
		$item = '';

		$iterator = 0;

        // TYPO3 7.0+
        foreach ( $partGroups as $partGroup ) {
            $fieldId = uniqid('tceforms-select-');
            $options = '<option value=""></option>';
            foreach ( $parts[$partGroup['uid']] as $part ) {
                $selected = '';

                if ( $this->checkIfPartIsSelectedInConfiguration($configurationUid, $part['uid']) ) {
                    $selected = ' selected="selected"';
                }

                $options .= "<option value=\"{$part['uid']}\"{$selected}>{$part['title']}</option>";
            }
            $item .= "<hr style=\"border-bottom: 1px solid #ddd;height:0\" /><label for=\"{$fieldId}\">{$partGroup['title']} :</label>";
            $item .= ('
					<div class="form-control-wrap">
						<div class="input-group">
							<span class="input-group-addon input-group-icon">
								<span alt="Insert Plugin" title="Insert Plugin">
									<span class="icon icon-size-small icon-state-default icon-content-plugin">
										<span class="icon-markup"><img src="' . CoreUtility\ExtensionManagementUtility::extRelPath('ecom_config_code_generator') . 'Resources/Public/Icons/tx_ecomconfigcodegenerator_domain_model_part.png" height="16" width="16"></span>
									</span>
								</span>
							</span>
							<select id="' . $fieldId . '" name="' . $PA['itemFormElName'] . '[' . $iterator . ']" data-formengine-validation-rules="[{\'type\':\'select\',\'minItems\':1,\'maxItems\':1}]" class="form-control form-control-adapt">' . $options . '</select>
						</div>
					</div>
				');
            $iterator++;
        }

		return $item;
	}

	/**
	 * itemsProcFuncEcomSkuGeneratorDomainModelDependentNoteDependentParts function.
	 *
	 * @param array $PA
	 * @param       $pObj
	 *
	 * @return void
	 */
	public function itemsProcFuncEcomSkuGeneratorDomainModelDependentNoteDependentParts(array &$PA, $pObj = NULL)  {
		// Adding an item!
		//$PA['items'][] = array($pObj->sL('Added label by PHP function|Tilfjet Dansk tekst med PHP funktion'), 999);

		if ( sizeof($PA['items']) ) {
			$partGroupsCollection = [ ];

			/** @var \TYPO3\CMS\Core\Database\DatabaseConnection $db */
			$db = $GLOBALS['TYPO3_DB'];
			$partGroups = [ ];
			$result = $db->exec_SELECTquery('uid', 'tx_ecomskugenerator_domain_model_partgroup', "pid={$PA['row']['pid']}");
			while ( $row = $db->sql_fetch_assoc($result) ) {
				$partGroups[] = $row['uid'];
			}
			$db->sql_free_result($result);

			foreach ( $PA['items'] as $item ) {
				$data = BackendUtility\BackendUtility::getRecord('tx_ecomskugenerator_domain_model_part', $item[1], '*');
				if ( !sizeof($data) || !CoreUtility\GeneralUtility::inList(implode(',', $partGroups), $data['part_group']) ) {
					continue;
				}

				$item[2] = 'clear.gif';
				$partGroupsCollection[0]['div'] = '-- not assigned --';
				if ( CoreUtility\MathUtility::canBeInterpretedAsInteger($data['part_group']) ) {
					if ( !array_key_exists($data['part_group'], $partGroupsCollection) ) {
						$partGroup = BackendUtility\BackendUtility::getRecord('tx_ecomskugenerator_domain_model_partgroup', $data['part_group'], 'title');
						$partGroupsCollection[$data['part_group']]['div'] = $partGroup['title'];
					}
					$partGroupsCollection[$data['part_group']]['items'][] = $item;
				} else {
					$partGroupsCollection[0]['items'][] = $item;
				}

			}
			//usort($configurationPackages, 'self::cmp'); // Sort Alphabetically @package label
			ksort($partGroupsCollection); // Order by uid @package

			$PA['items'] = [ ];
			foreach ( $partGroupsCollection as $partGroup ) {
				if ( !is_array($partGroup['items']) ) {
					continue;
				}
				$PA['items'][] = [
					$partGroup['div'],
					'--div--'
				];
				$PA['items'] = array_merge($PA['items'], $partGroup['items']);
			}
		} elseif ( !$PA['row']['part_groups'] ) {
			$PA['items'] = [ ];
		}

		// No return - the $PA and $pObj variables are passed by reference, so just change content in then and it is passed back automatically...
	}

	/**
     * ONLY FOR "ext:ecom_configcode_generator" at the moment!!
     *
	 * Check if pricing is fixed or percentage
	 *
	 * @param array $PA
	 *
	 * @return boolean
	 */
	/*public function checkPriceHandling($PA) {
		$partGroup = BackendUtility\BackendUtility::getRecord('tx_ecomconfigcodegenerator_domain_model_partgroup', $PA['record']['part_group'][0], 'settings');
		$check = ($partGroup['settings'] & Setup::BIT_PARTGROUP_USE_PERCENTAGE_PRICING) === Setup::BIT_PARTGROUP_USE_PERCENTAGE_PRICING;

		switch ( $PA['conditionParameters'][0] ) {
			case '1':
				return !$check;
			default:
				return $check;
		}
	}*/

	/**
	 * @return \TYPO3\CMS\Lang\LanguageService
	 */
	private function getLanguageService() {
		return $GLOBALS['LANG'];
	}

	/**
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	private function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
	}

    /**
     * Check if the current Part is selected in a SKU Configuration
     * @param int $configurationUid
     * @param int $partUid
     *
     * @return bool
     */
    private function checkIfPartIsSelectedInConfiguration($configurationUid, $partUid) {
        $db = $this->getDatabaseConnection();
        $return = $db->exec_SELECTcountRows('*', 'tx_ecomskugenerator_configuration_part_mm', "uid_local={$configurationUid} AND uid_foreign={$partUid}");
        return ($return) ? true : false;
    }

}

?>