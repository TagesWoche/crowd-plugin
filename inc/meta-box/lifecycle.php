<?php

namespace Crowd;


class MetaBoxLifecycle {
	
	const REQUEST_ROOT_ID = "lifecycle_root_id";
	const ERROR_KEY = "_crowd_error_transient";
	
	/**
	 * @var BaseCard
	 */
	private $card;
	
	/**
	 * MetaBoxLifecycle constructor.
	 *
	 * @param Plugin $plugin
	 */
	function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
	}
	
	function setCard($card){
		$this->card = $card;
	}
	
	/**
	 * @param \WP_Post $post
	 */
	function render(\WP_Post $post) {
		$root_post_id = "";
		$request_key  = self::REQUEST_ROOT_ID;
		if ( isset( $_GET[ $request_key ] ) ) {
			$root_post_id = $_GET[ $request_key ];
		}
		
		echo "<input type='text' name='{$request_key}' value='{$root_post_id}' />";
		
		$error = get_transient(self::ERROR_KEY);
		if(false !== $error){
			echo "<p class='error'>{$error}</p>";
			delete_transient(self::ERROR_KEY);
		}
		
		// TODO: show lifecycle
		echo "LIFECYCLE -> get all associated cards and contents and show them in line<br>";
		echo "before you can add a new card or content you have to save this one";
	}
	
	function save_post( $post_id ) {
		/**
		 * if there is no root id we are not responsible for saving anything
		 */
		if ( ! isset( $_POST[ self::REQUEST_ROOT_ID ] ) ) {
			return;
		}
		
		if ( ! empty( $_POST[ self::REQUEST_ROOT_ID ] ) ) {
			$root_id = sanitize_key( $_POST[ self::REQUEST_ROOT_ID ] );
			if ( $root_id != $post_id ) {
				/**
				 * if not root check if we should add the post id to lifecycle
				 */
				$ids = get_post_meta($root_id,BaseCard::META_LIFECYCLE_IDS,true);
				if(!is_array($ids)){
					if(!in_array($post_id, $ids)){
						/**
						 * if post_id is not in lifecycle ids append it
						 */
						$ids[] = $post_id;
						update_post_meta($root_id, BaseCard::META_LIFECYCLE_IDS, $ids);
					}
					
				} else {
					/**
					 * if lifecycle is no array in root meta show error
					 */
					set_transient(self::ERROR_KEY,"Root with id: {$root_id} has no lifecycle ids.",10);
				}
			}
			
			update_post_meta( $post_id, BaseCard::META_ROOT_ID, $root_id );
		} else {
			/**
			 * if root is empty set self to root
			 */
			update_post_meta( $post_id, BaseCard::META_LIFECYCLE_IDS, array($post_id) );
			update_post_meta( $post_id, BaseCard::META_ROOT_ID, $post_id );
		}
		
	}
	
}