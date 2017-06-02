<?php

/**
 * get global object
 * @return \Crowd\Plugin
 */
function crowd_get_plugin(){
	return \Crowd\Plugin::instance();
}

/**
 * get slug of crowd card post type
 *
 * @return string
 */
function crowd_get_card_post_type(){
	$plugin = crowd_get_plugin();
	return $plugin->card_post_type->getSlug();
}

/**
 * @param null $post_id
 *
 * @return WP_User|null
 */
function crowd_get_initiator($post_id = null){
	if(null == $post_id){
		global $post;
	} else {
		$post = get_post($post_id);
	}
	$initiator_id=get_post_meta( $post->ID, \Crowd\MetaBoxInitiator::META_INITIATOR, TRUE );
	$initiator = null;
	if ( FALSE != $initiator_id && "" != $initiator_id ) {
		/**
		 * @var \WP_User $initiator
		 */
		$initiator = get_user_by("ID", $initiator_id);
	}
	return $initiator;
}

/**
 * renders initiator by id or for global post
 * @param null $post_id
 */
function crowd_render_initiator( $post_id = null ){
	do_action(\Crowd\Plugin::ACTION_RENDER_INITIATOR, $post_id);
}

/**
 * renders cards that are connected to a post
 * @param null $post_id
 */
function crowd_render_post_cards($post_id = null){
	do_action(\Crowd\Plugin::ACTION_RENDER_POST_CARDS, $post_id);
}

/**
 * renders a single card card
 * @param null $card_id
 */
function crowd_render_card($card_id = null){
	do_action(\Crowd\Plugin::ACTION_RENDER_CARD, $card_id);
}