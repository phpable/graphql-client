<?php
namespace Able\GraphQL\Client\Utilities\Abstractions;

use \Able\Helpers\Arr;
use \Able\Helpers\Src;
use \Able\Helpers\Str;

use \Exception;

abstract class AAction {

	/**
	 * @var string
	 */
	protected static string $point = '';

	/**
	 * @var string
	 */
	protected static string $representer = ARepresenter::class;

	/**
	 * @var string[]
	 */
	private array $Fields = [];

	/**
	 * @var AProvider|null
	 */
	private ?AProvider $Provider = null;

	/**
	 * @param AProvider $Provider
	 */
	public function __construct(AProvider $Provider){
		foreach (array_keys(get_class_vars(get_class($this))) as $_) {

			if (preg_match('/^field([A-Za-z0-9_-]+$)/', $_, $Matches)) {
				array_push($this->Fields, Src::fcm($Matches[1]));
			}
		}

//		_dumpe($this->Fields);

		$this->Provider = $Provider;
	}

	public final function execute(): AFieldset {
		if (empty(static::$point)) {
			throw new Exception('Invalid access point!');
		}

		foreach ($this->Variables as $name => $value) {
			$this->Provider->register($name, $value);
		}

		$Fieldset = new static::$representer();

		$e = sprintf('mutation%s{%s{%s}}', call_user_func(function (){
			return $this->compact();
		}), call_user_func(function (){
			return static::$point . $this->inject();
		}, static::$point), $Fieldset->present());

//		_dumpe(__FILE__, $e);

		$Fieldset->parse($this->Provider->provide($e)['data'][static::$point]);

		return $Fieldset;
	}

	/**
	 * @return string
	 */
	protected final function compact(): string {
		return count($this->Variables) > 0 ? sprintf('(%s)', implode(', ', array_map(function($_){
			return sprintf('$%s: %s', $_, Src::tcm(gettype($this->Variables[$_])));
		}, array_keys($this->Variables)))) : '';
	}

	/**
	 * @return string
	 */
	protected final function inject(): string {
		return count($this->Variables) > 0 ? sprintf('(%s)', implode(', ', array_map(function($_){
			return sprintf('%s: $%s', $_, $_);
		}, array_keys($this->Variables)))) : '';
	}

	/**
	 * @var array
	 */
	private array $Variables = [];

	/**
	 * @param string $name
	 * @param $value
	 * @return AAction
	 *
	 * @throws Exception
	 */
	public final function with(string $name, $value): AAction {
		if (!in_array($name, $this->Fields)) {
			throw new Exception(sprintf('Undefined variable: %s!', $name));
		}

		$this->Variables[$name] = $value;
		return $this;
	}


	/**
	 * @param string $name
	 * @param array $args
	 * @return AAction
	 *
	 * @throws Exception
	 */
	public function __call(string $name, array $args = []): AAction {
		if (!preg_match('/^with([A-Z][A-Za-z-0-9_-]+)$/', $name, $Matches)) {
			throw new Exception(sprintf('Undefined method: %s!', $name));
		}

		return $this->with(Src::fcm($Matches[1]), Arr::first($args));
	}

}
