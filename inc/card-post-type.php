<?php

namespace Crowd;


class CardPostType {
	
	const SLUG = "card";
	const VAR_FILTER_CARD = "card_filter";
	
	/**
	 * CardContentType constructor.
	 *
	 * @param Plugin $plugin
	 */
	function __construct(Plugin $plugin) {
		$this->plugin = $plugin;
		
		add_action( 'init', array($this,'init_post_type') );
		
		/**
		 * add column for card type
		 */
		add_filter( 'manage_'.$this->getSlug().'_posts_columns' , array($this,'manage_this_columns') );
		add_action( 'manage_'.$this->getSlug().'_posts_custom_column' , array($this, 'manage_this_custom_column'), 10, 2 );
		add_filter( 'manage_edit-'.$this->getSlug().'_sortable_columns', array($this,'manage_this_sortable_columns') );
		
		/**
		 * add filter for card types
		 */
		add_action( 'restrict_manage_posts', array($this, 'restrict_manage_posts') );
		add_filter( 'parse_query', array($this,'parse_query') );
		
	}
	
	/**
	 * content type for cards
	 */
	function init_post_type(){
		$labels  = array(
			'name'                  => _x( 'Cards', 'Post Type General Name', Plugin::DOMAIN ),
			'singular_name'         => _x( 'Card', 'Post Type Singular Name', Plugin::DOMAIN ),
			'menu_name'             => __( 'Card', Plugin::DOMAIN ),
			'name_admin_bar'        => __( 'Card', Plugin::DOMAIN ),
			'archives'              => __( 'Card', Plugin::DOMAIN ),
			'parent_item_colon'     => __( 'Parent Card:', Plugin::DOMAIN ),
			'all_items'             => __( 'Cards', Plugin::DOMAIN ),
			'add_new_item'          => __( 'Add Card', Plugin::DOMAIN ),
			'add_new'               => __( 'New Card', Plugin::DOMAIN ),
			'new_item'              => __( 'New', Plugin::DOMAIN ),
			'edit_item'             => __( 'Edit', Plugin::DOMAIN ),
			'update_item'           => __( 'Update', Plugin::DOMAIN ),
			'view_item'             => __( 'View', Plugin::DOMAIN ),
			'search_items'          => __( 'Search', Plugin::DOMAIN ),
			'not_found'             => __( 'Not found', Plugin::DOMAIN ),
			'not_found_in_trash'    => __( 'Not found in trash', Plugin::DOMAIN ),
			'featured_image'        => __( 'Featured image', Plugin::DOMAIN ),
			'set_featured_image'    => __( 'Set featured image', Plugin::DOMAIN ),
			'remove_featured_image' => __( 'Remove featured image', Plugin::DOMAIN ),
			'use_featured_image'    => __( 'Use as featured image', Plugin::DOMAIN ),
			'insert_into_item'      => __( 'Insert into item', Plugin::DOMAIN ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', Plugin::DOMAIN ),
			'items_list'            => __( 'Items list', Plugin::DOMAIN ),
			'items_list_navigation' => __( 'Items list navigation', Plugin::DOMAIN ),
			'filter_items_list'     => __( 'Filter items list', Plugin::DOMAIN ),
		);
		$rewrite = array(
			'slug'       => $this->getSlug(),
			'with_front' => true,
			'pages'      => true,
			'feeds'      => true,
		);
		$args    = array(
			'label'               => __( 'Cards', Plugin::DOMAIN ),
			'description'         => __( 'Adds custom post type card', Plugin::DOMAIN ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'revisions' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => Menu::SLUG,
			'menu_position'       => 5,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'rewrite'             => $rewrite,
			'capability_type'     => 'page',
		);
		register_post_type( $this->getSlug(), $args );
		
	}
	
	/**
	 * Add column
	 * @param $columns
	 *
	 * @return mixed
	 */
	function manage_this_columns($columns){
		$columns['column-'.self::SLUG] = __('Cardtype', Plugin::DOMAIN);
		return $columns;
	}
	
	/**
	 * content of column
	 * @param $column
	 * @param $post_id
	 */
	function manage_this_custom_column($column, $post_id){
		if($column != 'column-'.self::SLUG) return;
		$post = get_post($post_id);
		$card = CardClasses::get_card_object($post);
		echo $card->getName();
		
	}
	
	/**
	 * set new column sortable
	 * @param $columns
	 *
	 * @return mixed
	 */
	function manage_this_sortable_columns( $columns ) {
		$columns['column-'.self::SLUG] = self::SLUG;
		return $columns;
	}
	
	/**
	 * filter the cards
	 * @param string $post_type
	 */
	function restrict_manage_posts($post_type){

		if($this->getSlug() != $post_type) return;

		$classes = CardClasses::get_registered_classes();
		$selected_card = "";
		if(isset($_GET[self::VAR_FILTER_CARD])){
			$selected_card=$_GET[self::VAR_FILTER_CARD];
		}
		?>
		<select name="<?php echo self::VAR_FILTER_CARD; ?>">
			<option value=""><?php _e("All cards", Plugin::DOMAIN); ?></option>
			<?php
			foreach ($classes as $card){
				/**
				 * @var BaseCard $obj
				 */
				$obj = new $card();
				$is_selected = ($selected_card==$obj->getName())? "selected='selected'":"";
				echo "<option {$is_selected} value='{$obj->getName()}'>{$obj->getName()}</option>";
			}
			?>
		</select>
		<?php
	}
	
	/**
	 * @param \WP_Query $query
	 *
	 * @return mixed
	 */
	function parse_query($query){
		if( isset($_GET[self::VAR_FILTER_CARD]) && "" != $_GET[self::VAR_FILTER_CARD] ){
			$card_name = $_GET[self::VAR_FILTER_CARD];
			$classes = CardClasses::get_registered_classes();
			foreach ($classes as $card){
				/**
				 * @var BaseCard $obj
				 */
				$obj = new $card();
				if($obj->getName() == $card_name){
					$query->query_vars['meta_key'] = BaseCard::META_CLASSNAME;
					$query->query_vars['meta_value'] = $card;
					return $query;
				}
			}
		}
		return $query;
	}
	
	/**
	 * get slug of content type
	 * @return string
	 */
	function getSlug(){
		$slug = apply_filters( Plugin::FILTER_CARD_CPT_SLUG, _x(self::SLUG, 'Card content type slug', Plugin::DOMAIN) );
		return (is_string($slug) && !empty($slug))? self::SLUG: $slug;
	}
	
	
}