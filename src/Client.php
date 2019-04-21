<?php
namespace Able\GraphQL\Client;

use \Able\Helpers\Arr;
use \Exception;

class Client {

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
	 */
	public final function withQuery(string $query) {
		$this->query = $query;
	}

	/**
	 * @var array
	 */
	private array $Variables = [];

	/**
	 * @param string $name
	 * @param string $value
	 */
	public final function withVariable(string $name, string $value) {
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

		try {
//			if (($data =

			$data = file_get_contents($this->point, false,

					stream_context_create([
						'http' => [
							'method' => 'POST',
							'header' => $Headers,
							'content' => json_encode(['query' => $this->query, 'variables' => $this->Variables]),
						]
					]));

//					) == false) {
//				_dumpe(error_get_last());
//				throw new Exception(...array_values(Arr::only(error_get_last(), 'message', 'type')));
//			}

			return json_decode($data, true);


		}catch (\Throwable $Exception) {
			_dumpe($Exception);
		}

//		_dumpe($data);`
//		return json_decode($data, true);
	}
}
