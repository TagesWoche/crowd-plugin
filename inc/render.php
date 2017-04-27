<?php
/**
 * Created by PhpStorm.
 * User: edward
 * Date: 05.01.17
 * Time: 12:27
 */

namespace Crowd;


class Render {
	
	function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
		
		add_action( 'the_content', array( $this, 'card_content' ) );
		add_action( Plugin::ACTION_RENDER_POST_CARDS, array( $this, 'render_post_cards' ) );
		add_action( Plugin::ACTION_RENDER_CARD, array( $this, 'render_card' ) );
		add_action( Plugin::ACTION_RENDER_INITIATOR, array( $this, 'render_initiator' ) );
	}
	
	/**
	 * get the post we are talking about
	 *
	 * @param $post_id
	 *
	 * @return null|\WP_Post
	 */
	private function get_post( $post_id ) {
		$post = NULL;
		if ( $post_id == NULL ) {
			global $post;
		} else {
			$post = get_post( $post_id );
		}
		
		return $post;
	}
	
	/**
	 * render cards connected to post
	 *
	 * @param null|int $post_id
	 */
	function render_post_cards( $post_id = NULL ) {
		$post = $this->get_post( $post_id );
		/**
		 * get meta values, ids of card objects
		 */
		$ids = get_post_meta( $post->ID, MetaBoxCardsToPost::META_POST_CARDS, TRUE );
		if ( ! is_array( $ids ) ) {
			return;
		}
		
		/**
		 * collect all connected card objects
		 */
		$cards = array();
		foreach ( $ids as $id ) {
			$cards[] = CardClasses::get_card_object( get_post( $id ) );
		}
		
		/**
		 * render the template
		 */
		include $this->get_template_path( Plugin::TEMPLATE_CARDS );
		
	}
	
	/**
	 * @param string $content
	 *
	 * @return string
	 */
	function card_content( $content ) {
		global $post;
		
		/**
		 * ignore if not post type of cards
		 */
		if ( !is_a($post, '\WP_Post') || $post->post_type != $this->plugin->card_post_type->getSlug() ) {
			return $content;
		}
		
		/**
		 * @var BaseCard $card
		 */
		$card = CardClasses::get_card_object( $post );
		
		return $card->render();
		
	}
	
	/**
	 * render card of a post
	 *
	 * @param \WP_Post|null $post_id
	 */
	function render_card( $post_id = NULL ) {
		
		$post = $this->get_post( $post_id );
		
		/**
		 * nothing I will handle
		 */
		if ( NULL == $post || $post->post_type != $this->plugin->card_post_type->getSlug() ) {
			return;
		}
		
		/**
		 * if BaseCard there is no card chosen in post
		 * @var BaseCard $card
		 */
		$card = CardClasses::get_card_object( $post );
		if ( get_class( $card ) == BaseCard::class ) {
			return;
		}
		
		echo $card->render();
	}
	
	/**
	 * render initiator of a post
	 *
	 * @param \WP_Post|null $post_id
	 */
	function render_initiator( $post_id = NULL ) {
		
		$initiator = crowd_get_initiator( $post_id );
		
		include $this->get_template_path( Plugin::TEMPLATE_INITIATOR );
		
	}
	
	/**
	 * use template for rendering
	 */
	function get_template_path( $template ) {
		if ( $overridden_template = locate_template( Plugin::THEME_FOLDER . "/" . $template ) ) {
			return $overridden_template;
		}
		
		return $this->plugin->dir . '/templates/' . $template;
		
	}
	
}