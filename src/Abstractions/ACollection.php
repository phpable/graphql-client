<?php
namespace Able\GraphQL\Client\Utilities\Abstractions;

use Able\GraphQL\Client\Utilities\Connection;
use Able\GraphQL\Client\Utilities\Abstractions\AProvider;

abstract class ACollection {

	/**
	 * @var string
	 */
	protected static string $providerClass = AProvider::class;

//	public final function __construct() {
//		$this->Client = new Client()
//	}
}
