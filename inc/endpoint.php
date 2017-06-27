<?php

namespace Crowd;


class Endpoint {
	
	/**
	 * url to api
	 */
	const URL = "__crowd";
	const VAR_ACTION = "crowd_action";
	
	/**
	 * request keys
	 */
	const VAR_IS_API = "is_crowd_api";
	
	/**
	 * if endpoint could not find any handler
	 * @var boolean
	 */
	public $show_404;
	
	/**
	 * FormAction constructor.
	 *
	 * @param Plugin $plugin
	 */
	function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
		$this->show_404 = false;
		
		/**
		 * get action handler classes
		 */
		require_once "endpoint/_abstract-action.php";
		require_once "endpoint/card-action.php";
		require_once "endpoint/backend-action.php";
		
		/**
		 * register api url
		 */
		add_action( 'init', array( $this, 'register_scripts' ), 0 );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );
		add_action( 'init', array( $this, 'add_endpoint' ), 0 );
		add_action( 'parse_request', array( $this, 'sniff_requests' ), 0 );
		
		add_action('parse_query', array($this, 'parse_query'));
		
	}
	
	function register_scripts(){
		wp_register_script( Plugin::HANDLE_JS_API, $this->plugin->url . 'js/api.js', array( 'jquery' ), 1, TRUE );
		
		
		wp_localize_script( Plugin::HANDLE_JS_API, 'Crowd_API', array(
			"endpoint" => "/".Endpoint::URL,
		) );
	}
	
	/**
	 * add query vars for endpoint
	 */
	function add_query_vars( $vars ) {
		$vars[] = self::VAR_IS_API;
		
		return $vars;
	}
	
	/**
	 *    Add API Endpoint
	 *    This is where the magic happens
	 * @return void
	 */
	public function add_endpoint() {
		add_rewrite_rule(
			'^'.self::URL.'/?$',
			'index.php?' . self::VAR_IS_API . '=1',
			'top'
		);
	}
	
	/**
	 *    Sniff Requests
	 */
	public function sniff_requests() {
		global $wp;
		if ( ! empty( $wp->query_vars[ self::VAR_IS_API ] )
		     && $wp->query_vars[ self::VAR_IS_API ] == 1
		     && $_SERVER['REQUEST_METHOD'] == 'POST'
		) {
			/**
			 * is a request for api
			 */
			$json = json_decode( file_get_contents( "php://input" ) );
			if(null != $json){
				// with json body
				$this->try_to_handle_request( $json );
			} else {
				// form submissions
				$this->try_to_handle_request((object) $_POST );
			}
			
		}
	}
	
	/**
	 * handle api request
	 *
	 * @param object $json from POST input
	 */
	private function try_to_handle_request( $json ) {
		
		// no action no ACTION
		if(isset($json->{self::VAR_ACTION})){
			// search for handler class
			$classes = get_declared_classes();
			foreach ( $classes as $class ) {
				
				if ( is_subclass_of( $class, "Crowd\\AbstractAction" ) ) {
					/**
					 * @var AbstractAction $obj
					 */
					$obj = new $class();
					if ( $obj->getActionSlug() == $json->{self::VAR_ACTION} ) {
						
						$obj->handle( $json );
						
					}
				}
			}
		}
		
		// could not handle anything? SHOW 404
		$this->show_404 = true;
	}
	
	/**
	 * if found 404 while handling set query to 404
	 * @param \WP_Query $wp_query
	 */
	function parse_query(\WP_Query $wp_query){
		if($this->show_404) $wp_query->set_404();
	}
	
}