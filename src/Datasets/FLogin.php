<?php
namespace Able\GraphQL\Client\Datasets;

use \Able\GraphQL\Client\Datasets\FUser;
use \Able\GraphQL\Client\Abstractions\AFieldset;

class FLogin extends AFieldset {

	/**
	 * @var string[]
	 */
	protected static array $Prototype = [
		'token',
		'expiresIn',
		'user',
	];

	/**
	 * @return string
	 */
	protected final function presentUserProperty(): string {
		return sprintf('user{%s}', $this->user->present());
	}

	/**
	 * @param array $data
	 * @return FUser
	 */
	protected final function parseUserProperty(array $data): FUser{
		return $this->user->parse($data);
	}

	/**
	 * @return FUser
	 */
	protected function initUserProperty(): FUser {
		return new FUser();
	}

}
