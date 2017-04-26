<?php

namespace Crowd;


class Embed {
	/**
	 * oEmbed constructor.
	 *
	 * @param \Crowd\Plugin $plugin
	 */
	function __construct(Plugin $plugin) {
		$this->plugin = $plugin;

		wp_oembed_add_provider(network_site_url('/card/*'),network_site_url('/wp-json/oembed/'));
		/**
		 * manipulate oembed html
		 */
		add_filter('embed_oembed_html', array($this, 'render_html'), 99, 4);
		add_filter('embed_html', array($this, 'post_oembed_html'), 99 , 4);
		
		/**
		 * intercept template suggestion
		 */
		add_filter( 'template_include', array( $this, 'embed_template' ), 99 );
	}
	
	/**
	 * cached oembed html tinymce
	 *
	 * @param $html
	 * @param $url
	 * @param $attr
	 * @param $post_id
	 *
	 * @return string
	 */
	function render_html($html, $url, $attr, $post_id){
		
		$card_post_id = url_to_postid($url);
		
		if( 0 != $card_post_id && $this->plugin->card_post_type->getSlug() == get_post_type($card_post_id)){
			$card = CardClasses::get_card_object( get_post($card_post_id) );
			return $card->render();//"<div class=\"wp-embedded-content\">KARTE</div>";
		}
		
		return $html;
	}
	
	/**
	 * oembed provider content html
	 *
	 * @param String $output
	 * @param \WP_Post $post
	 * @param int $width
	 * @param int $height
	 *
	 * @return mixed
	 */
	function post_oembed_html( $output, $post, $width, $height ){
		
		if( $this->plugin->card_post_type->getSlug() == get_post_type($post)){
			$card = CardClasses::get_card_object( get_post($post->ID) );
			return $card->render();
		}
		
		return $output;
	}
	
	/**
	 * Add a new template when solr search is triggered
	 * @param $template
	 * @return string
	 */
	function embed_template( $template ) {

		if ( get_query_var('embed') == 'true' && get_post_type() == $this->plugin->card_post_type->getSlug() ) {
			/**
			 * return crowd plugin embed template
			 */
			return $this->plugin->render->get_template_path(Plugin::TEMPLATE_EMBED);
		}
		
		/**
		 * return WordPress default template
		 */
		return $template;
	}
	
}