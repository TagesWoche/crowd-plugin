<?php
/**
 * Created by PhpStorm.
 * User: edward
 * Date: 06.01.17
 * Time: 13:04
 */

namespace Crowd;


class Grid {
	
	function __construct($plugin) {
		$this->plugin = $plugin;
		add_action('grid_load_classes', array($this, 'grid_load_classes'));
		add_filter('grid_templates_paths', array($this, 'grid_templates_paths'));
	}
	
	function grid_load_classes(){
		require "inc/grid_crowed_cards_box.inc";
	}
	
	function grid_templates_paths($paths){
		$paths[] = $this->plugin->dir."/grid/templates";
		return $paths;
	}
}