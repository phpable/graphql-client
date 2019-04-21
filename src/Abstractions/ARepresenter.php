<?php
namespace Able\GraphQL\Client\Abstractions;

use \Able\Struct\AStruct;

abstract class ARepresenter extends AStruct {

	/**
	 * @param array $data
	 */
	public final function parse(array $data): void {
		_dumpe($this->keys());
	}

	/**
	 * @return string
	 */
	public final function present(): string {
		return implode("\n", array_map(function($_){
			return $this->mutate('present', $_, $_);
		}, $this->keys()));
	}

}
