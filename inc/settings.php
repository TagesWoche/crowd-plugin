<?php
/**
 * Created by PhpStorm.
 * User: edward
 * Date: 25.01.17
 * Time: 10:41
 */

namespace Crowd;


class Settings {
	
	const SLUG = "crowd_settings";
	
	/**
	 * Settings constructor.
	 *
	 * @param Plugin $plugin
	 */
	function __construct($plugin) {
		$this->plugin = $plugin;
		
		add_action( 'admin_menu', array($this, 'admin_menu') );
	}
	
	/**
	 * register admin menu paths
	 */
	public function admin_menu() {
		add_submenu_page(
			Menu::SLUG,
			__('Crowd › Settings', Plugin::DOMAIN),
			__('Settings', Plugin::DOMAIN),
			'manage_options',
			self::SLUG,
			array( $this, 'render_settings')
		);
		
	}
	
	/**
	 * render the settings page
	 */
	public function render_settings() {
		?>
		<div class="wrap">
			<h2>Settings</h2>
			<p>Public or not public post type, add cards to content rendering...</p>
		</div>
<?php
	}
}