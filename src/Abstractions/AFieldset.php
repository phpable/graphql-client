<?php
namespace Able\GraphQL\Client\Utilities\Abstractions;

use \Able\Prototypes\TDefault;
use \Able\Prototypes\IGettable;
use \Able\Prototypes\TMutatable;
use \Able\Prototypes\IArrayable;
use \Able\Prototypes\TAggregatable;

use \Able\Helpers\Arr;
use \Able\Reglib\Regex;

use \Exception;

abstract class AFieldset
	implements IGettable, IArrayable {

	use TDefault;
	use TMutatable;
	use TAggregatable;

	/**
	 * @var array
	 */
	protected static array $Prototype = [];

	/**
	 * @var array
	 */
	private array $Fields = [];

	/**
	 * @throws Exception
	 */
	public function __construct() {
		$this->Fields = Arr::combine(array_map(function($name) {
			if (Regex::checkVariable($name)) {
				return $name;
			}

			throw new Exception(sprintf('Invalid field name: %s!', $name));
		}, static::aggregate('Prototype')));

		foreach ($this->Fields as $name => $value) {
			$this->Fields[$name] = $this->mutate('init', $name, $value);
		}
	}

	/**
	 * @param string $name
	 * @return mixed|null
	 */
	public final function __get(string $name) {
		return $this->mutate('get', $name, Arr::get($this->Fields, $name));
	}

	/**
	 * @param string $name
	 * @return false
	 */
	public final function __isset(string $name): bool {
		return isset($this->Fields[$name]);
	}

	/**
	 * @return string
	 */
	public final function present(): string {
		return implode("\n", array_map(function ($_){
			return $this->mutate('present', $_, $_);
		}, array_keys($this->Fields)));
	}

	/**
	 * @return array
	 */
	public final function toArray(): array {
		return  $this->Fields;
	}

	/**
	 * @param array $raw
	 * @return $this
	 */
	public function parse(array $raw): AFieldset {
		foreach (array_keys($this->Fields) as $name) {
			if (isset($raw[$name])) {
				$this->Fields[$name] = $this->mutate('parse', $name, $raw[$name]);
			}
		}

		return $this;
	}
}
