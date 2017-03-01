<?php

namespace Crowd;


class MetaBoxInitiator {
	
	/**
	 * post meta key for initiator user id
	 */
	const META_INITIATOR = "crowd_initiator";
	
	/**
	 * MetaBoxCardConfig constructor.
	 *
	 * @param Plugin $plugin
	 * @param BaseCard $card
	 */
	function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
		
	}
	
	/**
	 * @param \WP_Post $post
	 */
	function render( \WP_Post $post ) {
		wp_enqueue_style(
			"crowd_initiator_style",
			$this->plugin->url . "/css/meta-box-initiator.css"
		);
		wp_enqueue_script(
			"crowd_initiator_script",
			$this->plugin->url . "/js/initiator.js",
			array( "jquery", Plugin::HANDLE_JS_API),
			1,
			TRUE
		);
		
		wp_localize_script("crowd_initiator_script", "Crowd_Initiator", array(
			"params" => array(
				Endpoint::VAR_ACTION => BackendAction::ACTION,
				BackendAction::FUNC => BackendAction::FUNC_QUERY_USERS,
			),
		));
		
		?>
		<div class="crowd__initiator">
			<input type="text" id="crowd-initiator" value="" />
			<input type="hidden" id="crowd-initiator--id" value="" />
			<div class="crowd__initiator--list"></div>
			
		</div>
		<input
				id="crowd-initiator-id-field"
				type="hidden"
				name="<?php echo self::META_INITIATOR; ?>"
				value="<?php echo get_post_meta($post->ID, self::META_INITIATOR, true); ?>"
		/>
	<?php
	}
	
	/**
	 * listen on every save_post
	 *
	 * @param $post_id
	 * @param $post
	 */
	function save_post( $post_id, $post ) {
		if(isset($_POST[self::META_INITIATOR]) && !empty($_POST[self::META_INITIATOR])){
			$user_id = intval($_POST[self::META_INITIATOR]);
			update_post_meta($post_id,self::META_INITIATOR, $user_id);
		}
	}
	
}