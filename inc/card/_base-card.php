<?php

namespace Crowd;


class BaseCard {
	
	/**
	 * meta keys
	 */
	const META_CLASSNAME = "crowd_card_classname";
	
	/**
	 * form fields for card
	 */
	const POST_NONCE_ACTION = "crowd_card_submit_nonce";
	const POST_NONCE_NAME = "crowd_card_nonce";
	
	/**
	 * response get parameter
	 */
	const VAR_RESPONSE = "card-response";
	
	/**
	 * BaseCard constructor.
	 *
	 * @param \WP_Post $post
	 */
	function __construct( $post = NULL ) {
		$this->post = $post;
	}
	
	/**
	 * classname without namespace as card type identifier
	 * @return string
	 */
	function getType(){
		return str_replace( __NAMESPACE__ . "\\", "", get_class( $this ) );
	}
	
	/**
	 * readable name of card class
	 */
	function getName() {
		return $this->getType();
	}
	
	/**
	 * for template name. only lowercase chars and _ allowed!
	 * @return string
	 */
	function getTemplateName() {
		return strtolower( str_replace( __NAMESPACE__ . "\\", "", get_class( $this ) ) );
	}
	
	/**
	 * get json representation
	 * @return array
	 */
	public function toJSON(){
		return array(
			"ID" => $this->post->ID,
			"post_title" => $this->post->post_title,
			"type" => $this->getType(),
			"type_name" => $this->getName(),
		);
	}
	
	/**
	 * structure of meta fields
	 * @return array
	 */
	public function getMetaFields() {
		
		/**
		 * get available cards selections
		 */
		$classes    = CardClasses::get_registered_classes();
		$selections = array();
		
		foreach ( $classes as $class ) {
			/**
			 * @var $obj BaseCard
			 */
			$obj          = new $class();
			$selections[] = array(
				"key"   => $class,
				"label" => $obj->getName(),
			);
		}
		
		/**
		 * return structure
		 */
		return array(
			array(
				"meta_key"   => self::META_CLASSNAME,
				"label"      => __( "Card type", Plugin::DOMAIN ),
				"type"       => "select",
				"selections" => $selections,
			),
		);
	}
	
	/**
	 * get value of meta field
	 *
	 * @param $key
	 *
	 * @return mixed
	 */
	function getValue( $key ) {
		return get_post_meta( $this->post->ID, $key, TRUE );
	}
	
	/**
	 * get the classname for card class
	 * @return mixed
	 */
	function getClassname() {
		return $this->getValue( self::META_CLASSNAME );
	}
	
	function get_ID() {
		return $this->post->ID;
	}
	
	/**
	 * get post title
	 * @return string
	 */
	function get_title() {
		return $this->post->post_title;
	}
	
	/**
	 * ----------------------------------------------------
	 * RENDERING
	 * ----------------------------------------------------
	 */
	
	/**
	 * render the card
	 * @return string
	 */
	function render() {
		
		$class = get_class( $this );
		$file  = "";
		
		$plugin = crowd_get_plugin();
		$render = $plugin->render;
		
		while ( FALSE != $class ) {
			/**
			 * @var BaseCard $obj
			 */
			$obj = new $class();
			
			/**
			 * get template path
			 */
			$template_name = sprintf( Plugin::TEMPLATE_CARD, $obj->getTemplateName() );
			$file          = $render->get_template_path( $template_name );
			
			/**
			 * check if template exists
			 */
			if ( file_exists( $file ) ) {
				break;
			}
			
			/**
			 * if template not exists check parent class
			 */
			$class = get_parent_class( $class );
		}
		
		ob_start();
		include $file;
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
	
	/**
	 * ----------------------------------------------------
	 * save handling
	 * ----------------------------------------------------
	 */
	function save( $field, $value ) {
		if ( is_array( $value ) ) {
			
			$value = $this->_get_not_empty_values($value);
			
		} else {
			/**
			 * all other sanitize and save
			 */
			$value = sanitize_text_field( $value );
		}
		update_post_meta( $this->post->ID, $field["meta_key"], $value );
	}
	
	/**
	 * get nested values
	 * @param $array
	 *
	 * @return array
	 */
	private function _get_not_empty_values($array){
		$items = array();
		foreach ($array as $item){
			
			$empty = true;
			
			/**
			 * check all keys
			 */
			foreach ($item as $key => $value){
				if(is_array($value)){
					$values = $this->_get_not_empty_values($value);
					if(count($values) > 0) $empty = false;
				} else {
					if($value != "") $empty = false;
				}
			}
			
			/**
			 * add to list if not empty
			 */
			if(!$empty) $items[] = $item;
			
		}
		return $items;
	}
	
	/**
	 * overwrite if want to handle CardAction requests
	 *
	 * @param $json
	 *
	 * @param bool $redirect if use redirect or respond as json
	 *
	 * @return array|object|string redirect return string else array or object
	 */
	function card_action( $json ) { }
	
	/**
	 * overwrite for default inputs rendering
	 */
	function render_inputs() {
		wp_nonce_field( self::POST_NONCE_ACTION, self::POST_NONCE_NAME );
	}
	
	/**
	 * get quer args for redirect
	 * @param array $args
	 *
	 * @return array
	 */
	function get_response_query_args($args = array()){
		$args[self::VAR_RESPONSE] = "success-".$this->post->ID;
		return $args;
	}
	
	/**
	 * if if box was submitted and redirected with success
	 * @return bool
	 */
	function hasSubmitSuccess(){
		return (!empty($_GET[self::VAR_RESPONSE]) && $_GET[self::VAR_RESPONSE] == "success-".$this->post->ID);
	}
	
	
}