<?php
/**
 * Plugin Name: Crowd
 * Plugin URI: http://www.palasthotel.de
 * Description: Journalism beginning with readers
 * Version: 1.0
 * Author: Palasthotel <rezeption@palasthotel.de> (in person: Edward Bock)
 * Author URI: http://www.palasthotel.de
 * Requires at least: 4.0
 * Tested up to: 4.7
 * License: http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 * @copyright Copyright (c) 2016, Palasthotel
 * @package Palasthotel\Crowd
 */

namespace Crowd;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Plugin
 */
class Plugin{
	
	/**
	 * ---------------------------------------------
	 * plugin constants
	 * ---------------------------------------------
	 */
	const VERSION = 1.0;
	const DOMAIN = "crowd";
	const HANDLE_JS_API = "crowd_js_api";
	
	/**
	 * ---------------------------------------------
	 * all outputs via themes
	 * ---------------------------------------------
	 */
	/**
	 * folder in theme
	 */
	const THEME_FOLDER = "crowd";
	/**
	 * list of cards connected to a post
	 */
	const TEMPLATE_CARDS = "cards.tpl.php";
	/**
	 * card templates with %s being $card->templateName()
	 */
	const TEMPLATE_CARD = "card-%s.tpl.php";
	/**
	 * initiator templates
	 */
	const TEMPLATE_INITIATOR = "initiator.tpl.php";
	
	/**
	 * oembed template
	 */
	const TEMPLATE_EMBED = "embed.php";
	
	/**
	 * ---------------------------------------------
	 * all actions
	 * ---------------------------------------------
	 */
	/**
	 * add new card class
	 */
	const ACTION_ADD_CARD_CLASS = "crowd_add_card_class";
	/**
	 * render cards connected to a post
	 */
	const ACTION_RENDER_POST_CARDS = "crowd_render_post_cards";
	/**
	 * render a card
	 */
	const ACTION_RENDER_CARD = "crowd_render_card";
	/**
	 * render an initiator
	 */
	const ACTION_RENDER_INITIATOR = "crowd_render_initiator";
	
	/**
	 * ---------------------------------------------
	 * all filters
	 * ---------------------------------------------
	 */
	/**
	 * modify slug for card content type
	 */
	const FILTER_CARD_CPT_SLUG = "crowd_card_content_type_slug";
	/**
	 * modify content initially input card user input
	 */
	const FILTER_CARD_INPUT_MODIFY_CONTENT = "crowd_card_input_modify_content";
	/**
	 * should send mail with user input or will you handle it custom way
	 */
	const FILTER_CARD_INPUT_HANDLE_CONTENT = "crowd_card_input_handle_content";
	/**
	 * should send mail with user input or will you handle it custom way
	 */
	const FILTER_CARD_INPUT_MAIL_SUBJECT = "crowd_card_input_mail_subject";
	
	
	/**
	 * Plugin constructor.
	 */
	function __construct() {
		/**
		 * base paths
		 */
		$this->dir = plugin_dir_path( __FILE__ );
		$this->url = plugin_dir_url( __FILE__ );
		
		/**
		 * load translations
		 */
		load_plugin_textdomain( Plugin::DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		
		/**
		 * can be used to get only card posts
		 */
		require_once "inc/card-query.php";
		
		/**
		 * card classes
		 */
		require_once "inc/card-classes.php";
		$this->classes = new CardClasses($this);
		
		/**
		 * menu
		 */
		require_once "inc/menu.php";
		$this->menu = new Menu($this);
		
		/**
		 * settings
		 */
		require_once "inc/settings.php";
		$this->settings = new Settings($this);
		
		/**
		 * post type for cards
		 */
		require_once "inc/card-post-type.php";
		$this->card_post_type =new CardPostType($this);
		
		/**
		 * content type for cards
		 */
		require_once "inc/meta-box.php";
		$this->meta_box = new MetaBox($this);
		
		/**
		 * the endpoint
		 */
		require_once "inc/endpoint.php";
		$this->endpoint = new Endpoint($this);
		
		/**
		 * the rendering
		 */
		require_once "inc/render.php";
		$this->render = new Render($this);
		
		/**
		 * grid implementation
		 */
		require_once "grid/grid.php";
		$this->grid = new Grid($this);
		
		/**
		 * oembed implementation
		 */
		require_once "inc/embed.php";
		$this->embed = new Embed($this);
		
		/**
		 * on activate or deactivate plugin
		 */
		register_activation_hook(__FILE__, array($this, "activation"));
		register_deactivation_hook(__FILE__, array($this, "deactivation"));
		
	}
	
	/**
	 * on plugin activation
	 */
	function activation(){
		$this->endpoint->add_endpoint();
		$this->card_post_type->init_post_type();
		flush_rewrite_rules();
	}
	
	/**
	 * on plugin deactivation
	 */
	function deactivation(){
		flush_rewrite_rules();
	}
}

/**
 * init and make it accessible
 */
global $crowd_plugin;
$crowd_plugin = new Plugin();

/**
 * all public functions
 */
include "public-functions.php";