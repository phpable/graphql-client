<?php
namespace Able\GraphQL\Client\Abstractions;

use \Able\GraphQL\Client\Utilities\Connector;

use \Able\Helpers\Arr;
use \Able\Helpers\Src;
use \Able\Helpers\Str;

use \Exception;

abstract class ARequest {

	/**
	 * @var string
	 */
	protected static string $key;

	/**
	 * @return string
	 */
	protected final function key(): string {
		return static::$key ?? Src::fcm(Src::rns(static::class));
	}

	/**
	 * @var string
	 */
	protected static string $type = ARepresenter::class;

//	/**
//	 * @var string[]
//	 */
//	private array $Fields = [];

	/**
	 * @var Connector
	 */
	private Connector $Connector;

	/**
	 * @param Connector $Connector
	 */
	public function __construct(Connector $Connector){
//		foreach (array_keys(get_class_vars(get_class($this))) as $_) {
//
//			if (preg_match('/^param([A-Za-z0-9_-]+$)/', $_, $Matches)) {
//				array_push($this->Fields, Src::fcm($Matches[1]));
//			}
//		}
//
//		_dumpe($this->Fields);

		$this->Connector = $Connector;
	}

	protected final function compile(array $Variables): string {
		_dumpe($Variables);
		return sprintf('mutation%s{%s{%s}}', call_user_func(function (){
			return $this->compact();
		}), call_user_func(function (){
			return sprintf('%s%s', $this->key(), $this->inject());
		}), $Fieldset->present());
	}

	/**
	 * @return AFieldset
	 * @throws Exception
	 */
	public final function execute(): AFieldset {
//		foreach ($this->Variables as $name => $value) {
//			$this->Connector->withVariable($name, $value);
//		}

		$Fieldset = new static::$type();

		$this->Connector->withQuery($q = $this->compile(get_class_vars(get_class($this))));
		_dumpe($q);

		$Fieldset->parse($r = Arr::apply($this->Connector->execute(), function($_){
			throw new Exception(Arr::first(Arr::first($_)));
		}, 'errors')['data'][$this->key()]);

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
	 * @param string $name
	 * @param $value
	 * @return ARequest
	 *
	 * @throws Exception
	 */
	public final function with(string $name, $value): ARequest {
		if (!property_exists($this, $name = sprintf('param%s', Src::tcm($name)))) {
			throw new Exception(sprintf('Undeclared parameter: %s!', $name));
		}

		$this->{$name} = $value;
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
			throw new Exception(sprintf('Undeclared method: %s!', $name));
		}

		return $this->with(Src::fcm($Matches[1]), Arr::first($args));
	}

}
