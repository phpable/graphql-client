<?php
namespace Able\GraphQL\Client\Abstractions;

use \Able\GraphQL\Client\Abstractions\ACollection;
use \Able\GraphQL\Client\Client;

use \Exception;

abstract class AProvider {

	/**
	 * @var Client|null
	 */
	private ?Client $Client = null;

	/**
	 * AProvider constructor.
	 * @param Client $Client
	 */
	public function __construct(Client $Client) {
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
		return $this->Client->execute();
	}

	/**
	 * @param string $name
	 * @param $value
	 */
	public final function register(string $name, $value): void {
		$this->Client->withVariable($name, $value);
	}
}
