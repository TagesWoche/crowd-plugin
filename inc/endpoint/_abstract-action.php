<?php

namespace Crowd;


abstract class AbstractAction {
	
	/**
	 * @return string
	 */
	abstract function getActionSlug();
	
	/**
	 * handle request
	 *
	 * @param object $json from post request
	 *
	 * @return array|object
	 */
	abstract function handle($json);
	
}