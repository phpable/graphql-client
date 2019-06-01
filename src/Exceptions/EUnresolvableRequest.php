<?php
namespace Able\GraphQL\Client\Exceptions;

use \Able\Exceptions\EUnresolvable;

class EUnresolvableRequest extends EUnresolvable {

	/**
	 * @var string
	 */
	protected static string $template = 'Unresolvable request!';
}
