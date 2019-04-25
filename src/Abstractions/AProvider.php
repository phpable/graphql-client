<?php
namespace Able\GraphQL\Client\Abstractions;

use \Able\GraphQL\Client\Abstractions\ACollection;
use \Able\GraphQL\Client\Connection;

use \Exception;

abstract class AProvider {

	/**
	 * @var Connection|null
	 */
	private ?Connection $Client = null;

	/**
	 * AProvider constructor.
	 * @param Connection $Client
	 */
	public function __construct(Connection $Client) {
		$this->Client = $Client;
	}

	/**
	 * @param string $request
	 * @return array
	 *
	 * @throws Exception
	 */
	public function provide(string $request): array /*: ACollection*/ {
		$this->Client->withQuery($request);

		$r = $this->Client->execute();
		_dumpe($r);

		return  $r;
	}

	/**
	 * @param string $name
	 * @param $value
	 */
	public final function register(string $name, $value): void {
		$this->Client->withVariable($name, $value);
	}
}
