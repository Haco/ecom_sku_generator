<?php
namespace S3b0\EcomSkuGenerator;

/**
 * Class Setup
 * @package S3b0\EcomSkuGenerator
 */
class Setup {

	const BIT_CURRENCY_PREPEND_SYMBOL = 1;
	const BIT_CURRENCY_ADD_WHITEPACE_BETWEEN_CURRENCY_AND_VALUE = 2;
	const BIT_CURRENCY_NUMBER_SEPARATORS_IN_US_FORMAT = 4;

	const BIT_PARTGROUP_MULTIPLE_SELECT = 1;

	/**
	 * @param \S3b0\EcomSkuGenerator\Domain\Model\Content $content
	 *
	 * @return string
	 */
	public static function getSessionStorageKey(\S3b0\EcomSkuGenerator\Domain\Model\Content $content) {
		return 'skug-' . $content->getUid();
	}

}