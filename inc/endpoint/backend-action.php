<?php

namespace Crowd;

/**
 * Class BackendAction
 * @package Crowd
 */
class BackendAction extends AbstractAction {
	
	/**
	 * value for Endpoint::VAR_ACTION
	 */
	const ACTION = "backend_action";
	
	/**
	 * which backend action to execute
	 */
	const FUNC = "func";
	const FUNC_QUERY_CARDS = "query_cards";
	const FUNC_QUERY_USERS = "query_users";

	/**
	 * @return string
	 */
	function getActionSlug(){
		return self::ACTION;
	}
	
	/**
	 * handle request
	 */
	function handle($json) {
		
		/**
		 * check permission
		 */
		if(!is_user_logged_in() || !current_user_can("edit_posts") ) return;
		
		switch ($json->{self::FUNC}){
			case self::FUNC_QUERY_CARDS:
				$this->_query_cards($json);
				break;
			case self::FUNC_QUERY_USERS:
				$this->_query_users($json);
				break;
		}
	}
	
	/**
	 * query cards
	 * @param $json
	 */
	private function _query_cards($json){
		$query = new CardQuery(array(
			"s" => $json->s,
			"post__not_in" => (isset($json->ids))? $json->ids: array(),
		));
		$response = array();
		global $post;
		while ($query->have_posts()){
			$query->the_post();
			$card = CardClasses::get_card_object($post);
			$response[] = $card->toJSON();
		}
		
		wp_send_json($response);
		exit;
	}
	
	/**
	 * query users
	 * @param $json
	 */
	private function _query_users($json){
		
		$response = array();
		
		$user_query = new \WP_User_Query( array( 'search' => $json->s ) );
		if ( ! empty( $user_query->results ) ) {
			foreach ( $user_query->results as $user ) {
				/**
				 * @var \WP_User $user
				 */
				$response[] = array(
					"display_name" => $user->display_name,
					"ID" => $user->ID,
				);
			}
		}
		
		wp_send_json($response);
		exit;
	}


}