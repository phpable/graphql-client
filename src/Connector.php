<?php
namespace Able\GraphQL\Client;

use \Able\Helpers\Arr;
use \Able\GraphQL\Client\Exceptions\EUnresolvableRequest;

use \Exception;

class Connector {

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
		$this->query = trim($query);
	}

	/**
	 * @var array
	 */
	private array $Variables = [];

	/**
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public final function withVariable(string $name, $value): void {
		if (!is_scalar($value)) {
			throw new Exception(sprintf('Invalid argument type: %s!', gettype($value)));
		}

		$this->Variables[$name] = $value;
	}

	/**
	 * @param array $Values
	 * @return void
	 *
	 * @throws Exception
	 */
	public final function withVariables(array $Values): void {
		foreach($Values as $name => $value) {

			/**
			 * The only string as indexes are supported.
			 */
			if (!is_string($name)) {
				throw new Exception(sprintf('Unsupported index type: %s!', gettype($name)));
			}

			$this->withVariable($name, $value);
		}
	}

	/**
	 * @return array
	 *
	 * @throws Exception
	 * @throws EUnresolvableRequest
	 */
	public final function execute(): array {
		if (is_null($this->point)
			|| !filter_var($this->point, FILTER_VALIDATE_URL)) {

				throw new Exception('Undefined or invalid access point!');
		}

		if (is_null($this->query)
			|| !preg_match('/^(?:query|mutation)\s*(:?\([^)]+\))?\s*{.*}$/s', $this->query)) {

				throw new Exception('The query is empty or not well-formed!');
		}

		$Headers = [
			'Content-Type: application/json',
			'User-Agent: Able GraphQL Client'
		];

		if (!is_null($this->token)) {
			$Headers[] = sprintf("Authorization: Bearer %s", $this->token);
		}

		try {
			return Arr::cast(json_decode(file_get_contents($this->point, false,
				stream_context_create([
					'http' => [
						'method' => 'POST',
						'header' => $Headers,

						'content' => json_encode([
							'query' => $this->query,
							'variables' => $this->Variables
						]),
					]
			])), true, 512, JSON_THROW_ON_ERROR));
		} catch (\Throwable $Exception) {
			throw new EUnresolvableRequest($Exception);
		}
	}
}

