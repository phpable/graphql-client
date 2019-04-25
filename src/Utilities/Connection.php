<?php
namespace Able\GraphQL\Client\Utilities;

use \Able\Helpers\Arr;
use \Exception;

class Connection {

	/**
	 * @var string|null
	 */
	private ?string $point = null;

	/**
	 * @param string $point
	 */
	public function __construct(string $point) {
		$this->point = $point;
	}

	/**
	 * @var string|null
	 */
	private ?string $token = null;

	/**
	 * @param string $token
	 */
	public final function withToken(string $token): void {
		$this->token = $token;
	}

	/**
	 * @var string|null
	 */
	private ?string $query = null;

	/**
	 * @param string $query
	 * @return void
	 */
	public final function withQuery(string $query): void {
		$this->query = $query;
	}

	/**
	 * @var array
	 */
	private array $Variables = [];

	/**
	 * @param string $name
	 * @param string $value
	 * @return void
	 */
	public final function withVariable(string $name, string $value): void {
		$this->Variables[$name] = $value;
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	public final function execute(): array {
		$Headers = [
			'Content-Type: application/json',
			'User-Agent: Able GraphQL client'
		];

		if (!is_null($this->token)) {
			$Headers[] = sprintf("Authori zation: bearer %s", $this->token);
		}

		if (($rawData = @file_get_contents($this->point, false,
				stream_context_create([
					'http' => [
						'method' => 'POST',
						'header' => $Headers,

						'content' => json_encode([
							'query' => $this->query,
							'variables' => $this->Variables
						]),
					]
				]))

			) == false) {
				throw new \Exception(ucfirst(preg_replace('/^[^(]+\([^)]+\)\s*:\s*/', '', error_get_last()['message'])));
		}

		return json_decode($rawData, true);
	}
}
