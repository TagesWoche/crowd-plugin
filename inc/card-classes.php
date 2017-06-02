<?php

namespace Crowd;


class CardClasses {
	
	/**
	 * CardClasses constructor.
	 *
	 * @param Plugin $plugin
	 */
	function __construct(Plugin $plugin) {
		$this->plugin = $plugin;
		
		add_action( 'init', array($this,'init_classes') );
		add_action( Plugin::ACTION_ADD_CARD_CLASS, array($this, 'add_card_class') );
		
	}
	
	/**
	 * content type for cards
	 */
	function init_classes(){
		
		/**
		 * always laod base class first
		 */
		require_once 'card/_base-card.php';
		
		/**
		 * ask for card classes
		 */
		do_action(Plugin::ACTION_ADD_CARD_CLASS );
	}
	
	/**
	 * load core card classes
	 */
	function add_card_class(){
		require_once 'card/input-card.php';
		require_once 'card/poll-card.php';
		require_once 'card/html-card.php';
	}
	
	/**
	 * --------------------------
	 * utility
	 * --------------------------
	 */
	static function get_registered_classes(){
		$classes=get_declared_classes();
		$found_classes=array();
		$base = new BaseCard();
		foreach($classes as $class)
		{
			if(is_subclass_of($class,get_class($base)))
			{
				$found_classes[] = $class;
			}
		}
		return $found_classes;
	}
	
	/**
	 * @param \WP_Post $post
	 *
	 * @return BaseCard
	 */
	static function get_card_object( \WP_Post $post ) {
		$card      = new BaseCard( $post );
		$classname = $card->getClassname();
		if ( "" != $classname ) {
			$card = new $classname( $post );
		}
		return $card;
	}
	
}