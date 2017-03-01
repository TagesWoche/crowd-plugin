<?php

namespace Crowd;


class HTMLCard extends BaseCard {
	
	const META_PLAIN = "crowd_card_html_plain";
	
	function getName() {
		return "HTML Card";
	}
	
	public function getMetaFields(){
		
		$fields = parent::getMetaFields();
		
		$fields[] = array(
			"meta_key" => self::META_PLAIN,
			"label" => __("HTML", Plugin::DOMAIN),
			"type" => "textarea",
		);
		
		/**
		 * return structure
		 */
		return $fields;
	}
	
	function getHTML(){
		return $this->getValue(self::META_PLAIN);
	}
	
	function save( $field, $value ) {
		if(self::META_PLAIN == $field["meta_key"]){
			update_post_meta($this->post->ID,self::META_PLAIN, $value);
			return;
		}
		parent::save( $field, $value );
	}
}