<?php
namespace Able\GraphQL\Client\Abstractions;

use Able\GraphQL\Client\Utilities\Connector;
use \Able\Helpers\Arr;
use \Able\Helpers\Src;
use \Able\Helpers\Str;

use \Exception;

abstract class ARequest {

	/**
	 * @var string
	 */
	protected static string $handler = '';

	/**
	 * @var string
	 */
	protected static string $representer = ARepresenter::class;

	/**
	 * @var string[]
	 */
	private array $Fields = [];

	/**
	 * @var Connector
	 */
	private Connector $Connector;

	/**
	 * @param Connector $Connector
	 */
	public function __construct(Connector $Connector){
		foreach (array_keys(get_class_vars(get_class($this))) as $_) {

			if (preg_match('/^field([A-Za-z0-9_-]+$)/', $_, $Matches)) {
				array_push($this->Fields, Src::fcm($Matches[1]));
			}
		}

		$this->Connector = $Connector;
	}

	/**
	 * @return AFieldset
	 * @throws Exception
	 */
	public final function execute(): AFieldset {
		if (empty(static::$handler)) {
			throw new Exception('Invalid access point!');
		}

		foreach ($this->Variables as $name => $value) {
			$this->Connector->withVariable($name, $value);
		}

		$Fieldset = new static::$representer();

		$e = sprintf('mutation%s{%s{%s}}', call_user_func(function (){
			return $this->compact();
		}), call_user_func(function (){
			return static::$handler . $this->inject();
		}, static::$handler), $Fieldset->present());

		$this->Connector->withQuery($e);

		$Response = $this->Connector->execute();
		if (isset($Response['error'])) {
			throw new \Exception(Arr::first($Response['error']));
		}

		_dumpe(__FILE__, $this->Connector->execute());
//		$Fieldset->parse($this->Provider->provide($e)['data'][static::$handler]);
		$Fieldset->parse($this->Connector->execute()['data'][static::$handler]);

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
	 * @return ARequest
	 *
	 * @throws Exception
	 */
	public final function with(string $name, $value): ARequest {
		if (!in_array($name, $this->Fields)) {
			throw new Exception(sprintf('Undefined variable: %s!', $name));
		}

		$this->Variables[$name] = $value;
		return $this;
	}


	/**
	 * @param string $name
	 * @param array $args
	 * @return ARequest
	 *
	 * @throws Exception
	 */
	public function __call(string $name, array $args = []): ARequest {
		if (!preg_match('/^with([A-Z][A-Za-z-0-9_-]+)$/', $name, $Matches)) {
			throw new Exception(sprintf('Undefined method: %s!', $name));
		}

		return $this->with(Src::fcm($Matches[1]), Arr::first($args));
	}

}
