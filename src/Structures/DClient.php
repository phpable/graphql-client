<?php
namespace Able\GraphQL\Client;

use \Able\Struct\AStruct;
use \Able\Reglib\Regex;

use \Exception;

class SField extends AStruct {

	/**
	 * @var string[]
	 */
	protected static array $Prototype = [
		'name',
		'type',
	];

	/**
	 * @param string $value
	 * @return string
	 *
	 * @throws Exception
	 */
	protected final function setNameProperty(string $value): string {
 		if (!Regex::checkVariable($value)) {
			throw new \Exception(sprintf('Invalid field name: %s!', $value));
		}

		return $value;
	}
}
