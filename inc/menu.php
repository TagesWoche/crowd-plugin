<?php

namespace Crowd;


class Menu {
	
	const SLUG = "crowd";
	
	/**
	 * Menu constructor.
	 *
	 * @param \Crowd\Plugin $plugin
	 */
	function __construct(Plugin $plugin) {
		$this->plugin = $plugin;
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}
	
	/**
	 * register admin menu page for settings
	 */
	function admin_menu(){
		add_menu_page(
			_x('Crowd', 'Page title' ,Plugin::DOMAIN),
			__('Crowd', 'Menu title' ,Plugin::DOMAIN),
			'edit_posts',
			self::SLUG,
			null, // submenu pages will render output
			'dashicons-groups',// 'dashicons-universal-access',//
			25
		);
	}
}