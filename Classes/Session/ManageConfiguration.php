<?php
	/**
	 * Created by PhpStorm.
	 * User: S3b0
	 * Date: 09/09/15
	 * Time: 8:45 AM
	 */

	namespace S3b0\EcomSkuGenerator\Session;

	/**
	 * Class ManageConfiguration
	 * @package S3b0\EcomSkuGenerator\Session
	 */
	class ManageConfiguration {

		/**
		 * @param \S3b0\EcomSkuGenerator\Controller\BaseController $controller
		 * @param \S3b0\EcomSkuGenerator\Domain\Model\Part         $part
		 * @param array                                            $configuration
		 * @param bool                                             $setPartGroupActive
		 * @return void
		 */
		public static function addPartToConfiguration(\S3b0\EcomSkuGenerator\Controller\BaseController $controller, \S3b0\EcomSkuGenerator\Domain\Model\Part &$part, array &$configuration, $setPartGroupActive = TRUE) {
			$temp = &$configuration[$part->getPartGroup()->getUid()];

			// Add part
			if ( !$part->getPartGroup()->isMultipleSelectable() ) {
				$temp = [ ];
			}
			$temp[$part->getSorting()] = $part->getUid();
			$part->setActive(TRUE);
			$part->getPartGroup()->setActive($setPartGroupActive);
			$part->getPartGroup()->addActivePart($part);

			$controller->feSession->store('config', $configuration);
		}

		/**
		 * @param \S3b0\EcomSkuGenerator\Controller\BaseController $controller
		 * @param \S3b0\EcomSkuGenerator\Domain\Model\Part         $part
		 * @param array                                            $configuration
		 * @return void
		 */
		public static function removePartFromConfiguration(\S3b0\EcomSkuGenerator\Controller\BaseController $controller, \S3b0\EcomSkuGenerator\Domain\Model\Part &$part, array &$configuration) {
			$temp = &$configuration[$part->getPartGroup()->getUid()];

			if ( is_array($temp) ) {
				if( ( $key = array_search($part->getUid(), $temp) ) !== FALSE ) {
					unset($temp[$key]);
				}
			}
			$part->getPartGroup()->removeActivePart($part);
			if ( sizeof($temp) === 0 ) {
				unset($configuration[$part->getPartGroup()->getUid()]);
				$part->getPartGroup()->setActive(FALSE);
			}
			$part->setActive(FALSE);

			$controller->feSession->store('config', $configuration);
		}

		/**
		 * @param \S3b0\EcomSkuGenerator\Controller\BaseController $controller
		 * @param \S3b0\EcomSkuGenerator\Domain\Model\PartGroup    $partGroup
		 * @param array                                            $configuration
		 * @return void
		 */
		public static function removePartGroupFromConfiguration(\S3b0\EcomSkuGenerator\Controller\BaseController $controller, \S3b0\EcomSkuGenerator\Domain\Model\PartGroup &$partGroup, array &$configuration) {
			if ( $partGroup->getActiveParts() instanceof \TYPO3\CMS\Extbase\Persistence\ObjectStorage && $partGroup->getActiveParts()->count() ) {
				/** @var \S3b0\EcomSkuGenerator\Domain\Model\Part $part */
				foreach ( $partGroup->getActiveParts() as $part ) {
					$part->setActive(FALSE);
				}
			}
			unset($configuration[$partGroup->getUid()]);
			$partGroup->setActiveParts(new \TYPO3\CMS\Extbase\Persistence\ObjectStorage());
			$partGroup->setActive(FALSE);
			$controller->feSession->store('config', $configuration);
		}

		/**
		 * @param \S3b0\EcomSkuGenerator\Controller\BaseController $controller
		 * @return void
		 */
		public static function resetConfiguration(\S3b0\EcomSkuGenerator\Controller\BaseController $controller) {
			$controller->feSession->store('config', [ ]);
		}

	}