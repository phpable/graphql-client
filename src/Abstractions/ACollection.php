<?php
namespace Able\GraphQL\Client\Abstractions;

use \Able\GraphQL\Client\Connection;
use \Able\GraphQL\Client\Abstractions\AProvider;

abstract class ACollection {

	/**
	 * @var string
	 */
	protected static string $providerClass = AProvider::class;

//	public final function __construct() {
//		$this->Client = new Client()
//	}
}
