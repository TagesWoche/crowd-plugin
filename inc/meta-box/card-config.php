<?php

namespace Crowd;


class MetaBoxCardConfig {

	const ROOT_ID = "crowd-card-editor";
	const HANDLE_JS = "crowd-card-editor-js";
	const HANDLE_CSS = "crowd-card-editor-css";

	/**
	 * @var BaseCard
	 */
	private $card;

	/**
	 * MetaBoxCardConfig constructor.
	 *
	 * @param Plugin $plugin
	 * @param BaseCard $card
	 */
	function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10, 2 );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
	}

	/**
	 * @param $post_type
	 * @param \WP_Post $post
	 */
	function add_meta_boxes( $post_type, $post ) {
		if($post_type !== $this->plugin->card_post_type->getSlug() ) return;

		$card = CardClasses::get_card_object( $post );
		$this->setCard( $card );

		wp_enqueue_style(self::HANDLE_CSS, $this->plugin->url."/css/meta-box-card-editor.css");

		wp_enqueue_script(
			self::HANDLE_JS,
			$this->plugin->url . "/js/card-editor.js",
			array( 'jquery', 'underscore' ),
			filemtime( $this->plugin->dir . "/js/card-editor.js" ),
			true
		);

		$classes    = CardClasses::get_registered_classes();
		$structures = array();
		foreach ( $classes as $class ) {
			/**
			 * @var $obj BaseCard
			 */
			$obj                  = new $class();
			$structures[ $class ] = $obj->getMetaFields();
		}

		$state = $this->get_meta_values();

		wp_localize_script( self::HANDLE_JS, 'Crowd_CardEditor', array(
			"root_id"    => self::ROOT_ID,
			"structures" => $structures,
			"state"      => $state,
		) );

		/**
		 * card configurator only on post type card
		 */
		add_meta_box(
			'crowd_config_meta_box',
			__( 'Card configuration', Plugin::DOMAIN ),
			array( $this, 'render' ),
			$this->plugin->card_post_type->getSlug(),
			'normal',
			'high'
		);

	}

	/**
	 * get values for card meta structure
	 * @return array
	 */
	function get_meta_values() {
		$fields = $this->card->getMetaFields();
		$value  = array();
		foreach ( $fields as $field ) {
			$value[ $field['meta_key'] ] = $this->card->getValue( $field["meta_key"] );
		}

		return $value;
	}

	/**
	 * @param BaseCard $card
	 */
	function setCard( $card ) {
		$this->card = $card;
	}

	/**
	 * @param \WP_Post $post
	 */
	function render( \WP_Post $post ) {

		?>
		<div id="<?php echo self::ROOT_ID; ?>">CARD EDITOR</div>
		<?php

//		$fields = $this->card->getMetaFields();
//		$this->renderFields( $fields );

	}

	/**
	 * @param array $fields
	 * @param null|array $values
	 * @param string $parent_key
	 */
	function renderFields( $fields, $values = null, $parent_key = "" ) {

		// TODO: custom field handler with filter that return true if handled and echos output

		foreach ( $fields as $field ) {
			if ( null == $values ) {
				$value = $this->card->getValue( $field["meta_key"] );
			} else if ( isset( $values[ $field["meta_key"] ] ) ) {
				$value = $values[ $field["meta_key"] ];
			} else {
				$value = null;
			}
			switch ( $field["type"] ) {
				case "hidden":
					$this->renderHidden( $value, $field, $parent_key );
					break;
				case "text":
					$this->renderText( $value, $field, $parent_key );
					break;
				case "textarea":
					$this->renderTextarea( $value, $field, $parent_key );
					break;
				case "select":
					$this->renderSelect( $value, $field, $parent_key );
					break;
				case "list":
					$this->renderList( $value, $field, $parent_key );
					break;
				default:
					echo "<p>Unknown Type: {$field['type']} of meta field {$field['meta_key']}</p>";
					break;
			}
		}
	}

	/**
	 * @param $name
	 * @param $parents
	 *
	 * @return string
	 */
	function get_input_name( $name, $parents ) {
		if ( "" == $parents ) {
			return $name;
		}

		return $parents . "[{$name}]";
	}

	/**
	 * @param string $value
	 * @param array $field
	 * @param $parent_key
	 */
	function renderHidden( $value, $field, $parent_key = "" ) {
		echo "<input type='hidden' name='{$this->get_input_name($field["meta_key"],$parent_key)}' value='{$value}' /><br>";

	}

	/**
	 * @param string $value
	 * @param array $field
	 * @param $parent_key
	 */
	function renderText( $value, $field, $parent_key = "" ) {
		$label = $field["label"];
		echo "<label>{$label}<br><input type='text' name='{$this->get_input_name($field["meta_key"],$parent_key)}' value='{$value}' /></label><br>";
	}

	/**
	 * @param string $value
	 * @param array $field
	 * @param $parent_key
	 */
	function renderTextarea( $value, $field, $parent_key = "" ) {
		$label = $field["label"];
		echo "<label>{$label}<br><textarea name='{$this->get_input_name($field["meta_key"],$parent_key)}'>{$value}</textarea></label><br>";
	}

	/**
	 * @param string $card
	 * @param array $field
	 * @param $parent_key
	 */
	function renderSelect( $value, $field, $parent_key = "" ) {
		?>
		<label><?php echo $field["label"]; ?><br>
			<select name="<?php echo $this->get_input_name( $field["meta_key"], $parent_key ); ?>">
				<?php
				foreach ( $field["selections"] as $selection ) {
					$key      = $selection["key"];
					$label    = $selection["label"];
					$selected = ( $value == $key ) ? "selected=\"selected\"" : "";
					echo "<option {$selected} value=\"{$key}\">{$label}</option>";
				}
				?>
			</select></label><br>
		<?php

	}

	/**
	 * @param $values
	 * @param $field
	 * @param $parent_key
	 */
	function renderList( $values, $field, $parent_key = "" ) {

		if ( ! is_array( $values ) ) {
			$values = array();
		}

		echo "<p>{$field["label"]}<br>";

		if ( $parent_key == "" ) {
			$parent_key = $field["meta_key"];
		} else {
			$parent_key .= "[{$field['meta_key']}]";
		}

		$index = 0;
		foreach ( $values as $value ) {
			$this->renderFields( $field["structure"], $value, "{$parent_key}[{$index}]" );
			$index ++;
		}

		$this->renderFields( $field["structure"], "", "{$parent_key}[{$index}]" );

		echo "</p>";
	}

	/**
	 * listen on every save_post
	 *
	 * @param $post_id
	 * @param $post
	 */
	function save_post( $post_id, $post ) {

		if ( $post->post_type != $this->plugin->card_post_type->getSlug() ) {
			return;
		}

		if ( isset( $_POST[ BaseCard::META_CLASSNAME ] ) ) {

			$classes        = CardClasses::get_registered_classes();
			$post_classname = str_replace( "\\\\", "\\", $_POST[ BaseCard::META_CLASSNAME ] );

			foreach ( $classes as $class ) {
				if ( $class == $post_classname ) {
					$this->save_card( new $class( $post ) );
				}
			}
		}
	}

	/**
	 * @param BaseCard $card
	 */
	function save_card( $card ) {
		$fields = $card->getMetaFields();
		foreach ( $fields as $field ) {
			if ( isset( $_POST[ $field["meta_key"] ] ) ) {
				$card->save( $field, $_POST[ $field["meta_key"] ] );
			}
		}
	}
}