<?php
/**
 * Created by PhpStorm.
 * User: edward
 * Date: 04.01.17
 * Time: 13:31
 */

namespace Crowd;


class MetaBox {
	
	/**
	 * MetaBox constructor.
	 *
	 * @param Plugin $plugin
	 */
	function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
		
		require_once "meta-box/card-config.php";
		$this->config = new MetaBoxCardConfig($plugin);

		require_once "meta-box/initiator.php";
		$this->initiator = new MetaBoxInitiator($plugin);

		require_once "meta-box/cards-to-post.php";
		$this->post_cards = new MetaBoxCardsToPost($plugin);
		
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10, 2 );
	}
	
	function add_meta_boxes( $post_type, $post ) {
		
		/**
		 * add initiator
		 */
		add_meta_box(
			'crowd_initiator_meta_box',
			__( 'Initiator', Plugin::DOMAIN ),
			array( $this->initiator, 'render' ),
			"post",
			'side',
			'high'
		);
		
		/**
		 * add cards to post
		 */
		add_meta_box(
			'crowd_cards_to_post_meta_box',
			__( 'Talk to Me - Cards', Plugin::DOMAIN ),
			array( $this->post_cards, 'render' ),
			"post",
			'normal',
			'high'
		);
		
	}
	
}