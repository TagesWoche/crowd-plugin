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
			__('Talk to Me › Settings', Plugin::DOMAIN),
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

		if( isset($_POST) ){

			if(
				isset($_POST[Plugin::OPTION_AUTO_RENDER_CARDS_TO_POST_DISABLED]) &&
			    $_POST[Plugin::OPTION_AUTO_RENDER_CARDS_TO_POST_DISABLED] == "1"
			){

				update_site_option(	Plugin::OPTION_AUTO_RENDER_CARDS_TO_POST_DISABLED,true );
			} else {
				delete_site_option(	Plugin::OPTION_AUTO_RENDER_CARDS_TO_POST_DISABLED );
			}

		}

		?>
		<div class="wrap">
			<form method="post">
				<h2><?php _e('Settings', Plugin::DOMAIN) ?></h2>

				<p>
					<label>
						<input
								type="checkbox"
								name="<?php echo Plugin::OPTION_AUTO_RENDER_CARDS_TO_POST_DISABLED; ?>"
								value="1"
						        <?php
						        if( get_site_option(Plugin::OPTION_AUTO_RENDER_CARDS_TO_POST_DISABLED) ){
						        	?>
							        checked="checked"
							        <?php
						        }
						        ?>
						/>
						<?php _e('Disable autorendering cards to post content.', Plugin::DOMAIN); ?>
					</label>
				</p>

				<?php
				submit_button(__("Save", Plugin::DOMAIN), 'primary');
				?>

			</form>
		</div>
		<?php
	}
}