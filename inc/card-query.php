<?php

namespace Crowd;


class CardQuery extends \WP_Query {
	function __construct($args) {
		$args["post_type"] = crowd_get_card_post_type();
		parent::__construct($args);
	}
	
}