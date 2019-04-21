<?php
namespace Able\GraphQL\Client\Datasets;

use \Able\GraphQL\Client\Abstractions\AFieldset;

class FUser extends AFieldset {

	/**
	 * @var string[]
	 */
	protected static array $Prototype = [
		'id',
		'name',
		'email',
		'avatar',
	];
}
