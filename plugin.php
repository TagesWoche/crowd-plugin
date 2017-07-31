<?php
/**
 * Plugin Name: TalkToMe - user inputs & polls
 * Plugin URI: https://github.com/TagesWoche/crowd-plugin
 * Description: Journalism beginning with readers
 * Version: 1.0.8
 * Author: TagesWoche <admin@tageswoche.ch> & Palasthotel <rezeption@palasthotel.de>
 * Author URI: http://www.tageswoche.ch
 * Text Domain: crowd
 * Domain Path: /languages
 * Requires at least: 4.0
 * Tested up to: 4.8
 * License: http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 * @copyright Copyright (c) 2017, Palasthotel
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
class Plugin {
	
	/**
	 * ---------------------------------------------
	 * plugin constants
	 * ---------------------------------------------
	 */
	const DOMAIN = "crowd";
	const HANDLE_JS_API = "crowd_js_api";

	/**
	 * ---------------------------------------------
	 * values
	 * ---------------------------------------------
	 */
	const OPTION_AUTO_RENDER_CARDS_TO_POST_DISABLED = "_crowd_auto_render_cards_to_post_disabled";
	
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
	 * verify card input custom way
	 */
	const FILTER_CARD_INPUT_VERIFY_REQUEST = "crowd_card_input_verify_request";
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
	 * @var Plugin
	 */
	private static $instance;

	/**
	 * @return Plugin
	 */
	public static function instance(){
		if(self::$instance == null){
			self::$instance = new Plugin();
		}
		return self::$instance;
	}
	
	/**
	 * Plugin constructor.
	 */
	private function __construct() {
		/**
		 * base paths
		 */
		$this->dir = plugin_dir_path( __FILE__ );
		$this->url = plugin_dir_url( __FILE__ );
		
		/**
		 * load translations
		 */
		load_plugin_textdomain(
			Plugin::DOMAIN,
			FALSE,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		 );
		
		/**
		 * can be used to get only card posts
		 */
		require_once dirname( __FILE__ ) . "/inc/card-query.php";
		
		/**
		 * card classes
		 */
		require_once dirname( __FILE__ ) . "/inc/card-classes.php";
		$this->classes = new CardClasses( $this );
		
		/**
		 * menu
		 */
		require_once dirname( __FILE__ ) . "/inc/menu.php";
		$this->menu = new Menu( $this );
		
		/**
		 * settings
		 */
		require_once dirname( __FILE__ ) . "/inc/settings.php";
		$this->settings = new Settings( $this );
		
		/**
		 * post type for cards
		 */
		require_once dirname( __FILE__ ) . "/inc/card-post-type.php";
		$this->card_post_type = new CardPostType( $this );
		
		/**
		 * content type for cards
		 */
		require_once dirname( __FILE__ ) . "/inc/meta-box.php";
		$this->meta_box = new MetaBox( $this );
		
		/**
		 * the endpoint
		 */
		require_once dirname( __FILE__ ) . "/inc/endpoint.php";
		$this->endpoint = new Endpoint( $this );
		
		/**
		 * the rendering
		 */
		require_once dirname( __FILE__ ) . "/inc/render.php";
		$this->render = new Render( $this );

		/**
		 * post manipulation
		 */
		require_once dirname( __FILE__ ) . "/inc/post.php";
		$this->post = new Post( $this );
		
		/**
		 * grid implementation
		 */
		require_once dirname( __FILE__ ) . "/grid/grid.php";
		$this->grid = new Grid( $this );
		
		/**
		 * oembed implementation
		 */
		require_once dirname( __FILE__ ) . "/inc/embed.php";
		$this->embed = new Embed( $this );
		
		/**
		 * on activate or deactivate plugin
		 */
		register_activation_hook( __FILE__, array( $this, "activation" ) );
		register_deactivation_hook( __FILE__, array( $this, "deactivation" ) );
		
	}
	
	/**
	 * on plugin activation
	 */
	function activation() {
		$this->endpoint->add_endpoint();
		$this->card_post_type->init_post_type();
		flush_rewrite_rules();
	}
	
	/**
	 * on plugin deactivation
	 */
	function deactivation() {
		flush_rewrite_rules();
	}
}

/**
 * init and make it accessible
 */
Plugin::instance();

/**
 * all public functions
 */
include dirname( __FILE__ ) . "/public-functions.php";