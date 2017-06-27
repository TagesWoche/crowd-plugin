<?php

namespace Crowd;


class InputCard extends BaseCard {
	
	/**
	 * meta fields
	 */
	const META_RECEIVER = "crowd_card_input_card_receiver";
	const META_PLACEHOLDER_TEXT = "crowd_card_input_card_placeholder_text";
	const META_SUBMIT_BUTTON_LABEL = "crowd_card_input_card_submit_button_label";
	
	/**
	 * form action fields
	 */
	const POST_INPUT_USER_CONTENT = "crowd_card_input_user_content";
	
	/**
	 * InputCard constructor.
	 *
	 * @param \WP_Post $post
	 */
	function __construct( $post = NULL ) {
		parent::__construct( $post );
	}
	
	public function getMetaFields() {
		
		$fields = parent::getMetaFields();
		
		$fields[] = array(
			"meta_key" => self::META_RECEIVER,
			"label"    => __( "EMail of receiver", Plugin::DOMAIN ),
			"type"     => "text",
		);
		
		$fields[] = array(
			"meta_key" => self::META_PLACEHOLDER_TEXT,
			"label"    => __( "Input text field placeholder text", Plugin::DOMAIN ),
			"type"     => "text",
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
	function get_receiver() {
		return $this->getValue( self::META_RECEIVER );
	}
	
	function get_placeholder_text() {
		return $this->getValue( self::META_PLACEHOLDER_TEXT );
	}
	
	function get_submit_button_label() {
		return $this->getValue( self::META_SUBMIT_BUTTON_LABEL );
	}
	
	/**
	 * --------------------------------------------------------
	 * CardAction handler
	 * --------------------------------------------------------
	 */
	function card_action( $json, $redirect = FALSE ) {

		$error = false;

		/**
		 * if there is no user content we cannot help
		 */
		if ( ! isset( $json->{self::POST_INPUT_USER_CONTENT} ) ) {
			$error = true;
		}

		$verified = apply_filters(Plugin::FILTER_CARD_INPUT_VERIFY_REQUEST, null, $json, $this);
		if(!$verified){
			$error = true;
		}

		if(!$error){
			/**
			 * modify $content initially filled with user content
			 */
			$content = $json->{self::POST_INPUT_USER_CONTENT};
			$content = apply_filters( Plugin::FILTER_CARD_INPUT_MODIFY_CONTENT, $content, $json, $this );

			/**
			 * handle submitted content.
			 */
			$skip_mailing = FALSE;
			$skip_mailing = apply_filters( Plugin::FILTER_CARD_INPUT_HANDLE_CONTENT, $skip_mailing, $content, $json, $this );


			if ( ! $skip_mailing ) {

				/**
				 * send mail if should not skip
				 */
				$subject = apply_filters(
					Plugin::FILTER_CARD_INPUT_MAIL_SUBJECT,
					sprintf( _x( "New content from %s", "Mail subject", Plugin::DOMAIN ), $this->getName() ),
					$json,
					$this
				);

				wp_mail(
					$this->get_receiver(),
					$subject,
					$content
				);
			}
		}

		
		/**
		 * if redirect
		 */
		if ( isset( $json->{CardAction::VAR_REDIRECT} ) && "" != $json->{CardAction::VAR_REDIRECT} ) {
			wp_redirect( add_query_arg( $this->get_response_query_args( array("error" => $error) ), $json->{CardAction::VAR_REDIRECT} ) );
			exit;
		}
		
		/**
		 * else if json response
		 */
		header( "Content-Type: application/json; charset=UTF-8" );
		header( "Cache-Control: no-store, no-cache, must-revalidate, max-age=0" );
		header( "Cache-Control: post-check=0, pre-check=0", FALSE );
		header( "Pragma: no-cache" );
		echo json_encode(array(
			"error" => $error,
		));
		exit;
	}

	/**
	 * get quer args for redirect
	 *
	 * @param array $args
	 *
	 * @return array
	 * @internal param array $args
	 *
	 */
	function get_response_query_args($args = array()){
		$_args = parent::get_response_query_args();
		if($args["error"]){
			$_args[self::VAR_RESPONSE] = "error-".$this->post->ID;
		}
		return $_args;
	}
	
	
}