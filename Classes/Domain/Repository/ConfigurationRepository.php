<?php
namespace S3b0\EcomSkuGenerator\Domain\Repository;

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
use S3b0\EcomSkuGenerator\Domain\Model\Part;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * The repository for Configurations
 */
class ConfigurationRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * @param array $configuration
     * @param \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $partGroup
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findByConfigurationArray(array $configuration, \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $partGroup = null) {
        $query = $this->createQuery();
        $query->setQuerySettings($query->getQuerySettings()->setStoragePageIds([$GLOBALS['TSFE']->id]));

        $constraints = [];
        foreach ($configuration as $partGroupUid => $partGroupParts) {
            if ($partGroup instanceof \S3b0\EcomSkuGenerator\Domain\Model\PartGroup && $partGroupUid === $partGroup->getUid()) {
                continue;
            }
            foreach ($partGroupParts as $part) {
                $constraints[] = $query->contains('parts', $part);
            }
        }
        return sizeof($constraints) ? $query->matching($query->logicalAnd($constraints))->execute() : $query->execute();
    }

    /**
     * Check if parts are available in ANY configuration.
     * Returns an array of PartUids that are definitely available in ANY configuration
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $parts
     * @return array
     */
    public function getAvailablePartsInAnyConfiguration($parts) {
        $query = $this->createQuery();
        $query->setQuerySettings($query->getQuerySettings()->setStoragePageIds([$GLOBALS['TSFE']->id]));
        $availableParts = [];

        foreach ($parts as $part) {
            $result = $query->matching($query->contains('parts', $part))->execute();
            if (count($result) && $result instanceof QueryResultInterface) {
                $availableParts[] = $part->getUid();
            }
        }

        return $availableParts;
    }

    /**
     * @param array $configuration
     * @param \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $partGroup
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function findCompatiblePartsByConfigurationArray(array $configuration, \S3b0\EcomSkuGenerator\Domain\Model\PartGroup $partGroup) {
        $return = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();

        if ($configurations = $this->findByConfigurationArray($configuration, $partGroup)) {
            /** @var \S3b0\EcomSkuGenerator\Domain\Model\Configuration $configuration */
            foreach ($configurations as $configuration) {
                if ($configuration->getParts() instanceof \Countable) {
                    $return->addAll($configuration->getParts());
                }
            }
        }

        return $return;
    }

}