<?php
/**
 * Created by PhpStorm.
 * User: edward
 * Date: 02.06.17
 * Time: 12:10
 */

namespace Crowd;

/**
 * Class Post
 * @package Crowd
 */
class Post {
	/**
	 * Post constructor.
	 *
	 * @param Plugin $plugin
	 */
	function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
		add_filter( "the_content", array( $this, "the_content" ), 0 );
	}

	/**
	 * @param string $content
	 *
	 * @return string
	 */
	function the_content( $content ) {

		if (
			$this->plugin->card_post_type->getSlug() == get_post_type() ||
			get_site_option(Plugin::OPTION_AUTO_RENDER_CARDS_TO_POST_DISABLED)
		) {
			return $content;
		}

		$ids = get_post_meta( get_the_ID(), MetaBoxCardsToPost::META_POST_CARDS, true );

		if ( is_array( $ids ) && count( $ids ) > 0 ) {
			foreach ( $ids as $id ) {
				$card_post = get_post( $id );
				$card      = CardClasses::get_card_object( $card_post );
				$content   .= $card->render();
			}
		}

		return $content;
	}
}