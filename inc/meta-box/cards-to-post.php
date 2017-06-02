<?php

namespace Crowd;


class MetaBoxCardsToPost {
	
	/**
	 * post meta key for initiator user id
	 */
	const META_POST_CARDS = "crowd_post_cards";

	const NONCE_ACTION = "nonce_crowd_post_cards_action";
	const NONCE_NAME = "nonce_crowd_post_cards_name";
	
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
			"crowd_cards_to_post_style",
			$this->plugin->url . "/css/meta-box-cards-to-post.css"
		);
		wp_enqueue_script(
			"crowd_cards_to_post_script",
			$this->plugin->url . "/js/cards-to-post.js",
			array( "jquery", "jquery-ui-sortable", Plugin::HANDLE_JS_API ),
			filemtime($this->plugin->dir."/js/cards-to-post.js"),
			TRUE
		);
		
		$ids      = get_post_meta( $post->ID, self::META_POST_CARDS, TRUE );
		$contents = array();
		if ( is_array( $ids ) && count( $ids ) > 0 ) {
			foreach ($ids as $id){
				$card_post = get_post($id);
				$card       = CardClasses::get_card_object( $card_post );
				$contents[] = $card->toJSON();
			}
		}
		wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME)
		?>
		<script type="text/javascript">
			window.crowd = window.crowd || {};
			window.crowd.post_edit_pattern = "<?php echo admin_url( "post.php?post=%ID%&action=edit" ) ?>"
			window.crowd.post_edit_pattern_ID_placeholder = "%ID%";
			window.crowd.cards_to_post_input_name = "<?php echo self::META_POST_CARDS; ?>";
			window.crowd.cards_to_post = <?php echo json_encode( $contents ); ?>;
		</script>
		<div id="crowd-cards-to-post">
			<p>Loading cards...</p>
			<noscript>Please activate JavaScript...</noscript>
		</div>
		<?php
	}
	
	/**
	 * listen on every save_post
	 *
	 * @param $post_id
	 * @param $post
	 */
	function save_post( $post_id, $post ) {

		if( !isset($_POST[self::NONCE_NAME]) || !wp_verify_nonce($_POST[self::NONCE_NAME], self::NONCE_ACTION )) return;

		if ( isset( $_POST[ self::META_POST_CARDS ] )
		     && ! empty( $_POST[ self::META_POST_CARDS ] )
		     && is_array( $_POST[ self::META_POST_CARDS ] )
		) {
			$ids = array();
			foreach ( $_POST[ self::META_POST_CARDS ] as $card_id ) {
				$ids[] = intval( $card_id );
			}
			
			update_post_meta( $post_id, self::META_POST_CARDS, $ids );
		} else {
			delete_post_meta( $post_id, self::META_POST_CARDS);
		}
	}
	
}