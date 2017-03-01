<?php

namespace Crowd;


class PollCard extends BaseCard {
	
	const META_OPTIONS = "crowd_card_options";
	const META_SUBMIT_BUTTON_LABEL = "crowd_card_input_card_submit_button_label";
	
	const POST_SELECTED_OPTION = "crowd_card_selected_option";
	
	function getName() {
		return "Poll Card";
	}
	
	public function getMetaFields() {
		
		$fields = parent::getMetaFields();
		
		$fields[] = array(
			"meta_key"  => self::META_OPTIONS,
			"label"     => __( "Options", Plugin::DOMAIN ),
			"type"      => "list",
			"structure" => array(
				array(
					"meta_key" => "label",
					"label"    => __( "Option label", Plugin::DOMAIN ),
					"type"     => "text",
				),
				array(
					"meta_key" => "counter",
					"label"    => __( "Option counter", Plugin::DOMAIN ),
					"type"     => "text",
				),
			),
		);
		
		$fields[] = array(
			"meta_key" => self::META_SUBMIT_BUTTON_LABEL,
			"label"    => __( "Label of submit button", Plugin::DOMAIN ),
			"type"     => "text",
		);
		
		/**
		 * return structure
		 */
		return $fields;
	}
	
	/**
	 * --------------------------------------------------------
	 * getter for meta fields
	 * --------------------------------------------------------
	 */
	function get_options() {
		$val = $this->getValue( self::META_OPTIONS );
		
		return ( is_array( $val ) ) ? $val : array();
	}
	
	function get_submit_button_label() {
		return $this->getValue( self::META_SUBMIT_BUTTON_LABEL );
	}
	
	
	/**
	 * --------------------------------------------------------
	 * CardAction handler
	 * --------------------------------------------------------
	 */
	function card_action( $json ) {
		
		$options = $this->get_options();
		for ( $i = 0; $i < count( $options ); $i ++ ) {
			if ( $options[ $i ]["label"] == $json->{self::POST_SELECTED_OPTION} ) {
				$options[ $i ]["counter"] ++;
			}
		}
		$success = update_post_meta( $this->post->ID, self::META_OPTIONS, $options );
		
		/**
		 * if redirect
		 */
		if ( isset( $json->{CardAction::VAR_REDIRECT} ) && "" != $json->{CardAction::VAR_REDIRECT} ) {
			wp_redirect( add_query_arg( $this->get_response_query_args(), $json->{CardAction::VAR_REDIRECT} ) );
			exit;
		}
		
		/**
		 * else if json response
		 */
		header( "Content-Type: application/json; charset=UTF-8" );
		header( "Cache-Control: no-store, no-cache, must-revalidate, max-age=0" );
		header( "Cache-Control: post-check=0, pre-check=0", FALSE );
		header( "Pragma: no-cache" );
		
		echo json_encode( array( "success" => $success ) );
		
		exit;
		
	}
}