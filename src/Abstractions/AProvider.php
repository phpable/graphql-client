<?php
namespace Able\GraphQL\Client\Utilities\Abstractions;

use Able\GraphQL\Client\Utilities\Abstractions\ACollection;
use Able\GraphQL\Client\Utilities\Connector;

use \Exception;

abstract class AProvider {

	/**
	 * @var Connector|null
	 */
	private ?Connector $Client = null;

	/**
	 * AProvider constructor.
	 * @param Connector $Client
	 */
	public function __construct(Connector $Client) {
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
