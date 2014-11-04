<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.nilambar.net
 * @since      1.0.0
 *
 * @package    Woocommerce_Product_Tabs
 * @subpackage Woocommerce_Product_Tabs/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Product_Tabs
 * @subpackage Woocommerce_Product_Tabs/public
 * @author     Nilambar Sharma <nilambar@outlook.com>
 */
class Woocommerce_Product_Tabs_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	private $product_tabs_list;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->product_tabs_list = get_posts(
			array(
				'post_type'      => WOOCOMMERCE_PRODUCT_TABS_POST_TYPE_TAB,
				'posts_per_page' => -1,
				'orderby'        => 'menu_order',
				'order'          => 'asc',
				)
			);
		if ( ! empty( $this->product_tabs_list ) ) {
			foreach ($this->product_tabs_list as $key => $t) {
				$this->product_tabs_list[$key]->post_meta = get_post_meta($this->product_tabs_list[$key]->ID);
			}
		}


	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Product_Tabs_Public_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Product_Tabs_Public_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/plugin-name-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Product_Tabs_Public_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Product_Tabs_Public_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plugin-name-public.js', array( 'jquery' ), $this->version, false );

	}

	public function custom_woocommerce_product_tabs( $tabs ){


		if ( empty( $this->product_tabs_list ) ) {
			return $tabs;
		}

		$wpt_tabs = array();
		foreach ($this->product_tabs_list as $key => $prd) {
			$wpt_tabs[$key]['id'] = $prd->post_name;
			$wpt_tabs[$key]['title'] = esc_attr( $prd->post_title );
			$wpt_tabs[$key]['priority'] = esc_attr( $prd->menu_order );
		}

		if ( ! empty( $wpt_tabs ) ) {

			foreach ($wpt_tabs as $key => $tab) {
				$tab_temp             = array();
				$tab_temp['title']    = $tab['title'];
				$tab_temp['priority'] = $tab['priority'];
				$tab_temp['callback'] = array( $this, 'wpt_callback' );
				$tabs[$tab['id']]     = $tab_temp;
			}

		}

		return $tabs;

	}

	public function wpt_callback( $key, $tab ){

		global $product;

		$tab_post = get_page_by_path( $key, OBJECT, WOOCOMMERCE_PRODUCT_TABS_POST_TYPE_TAB );
		if (empty($tab_post)) {
			return;
		}
		$flag_wpt_option_use_default_for_all = get_post_meta( $tab_post->ID, '_wpt_option_use_default_for_all', true );
		if ( 'yes' == $flag_wpt_option_use_default_for_all ) {
			// Default content for all
			echo apply_filters( 'the_content', $tab_post->post_content );
		}
		else{
			// no default
			$tab_value = get_post_meta( $product->id, '_wpt_field_'.$key, true );
			if ( ! empty( $tab_value ) ) {
				// Value is set for Product
				echo apply_filters( 'the_content', $tab_value );
			}
			else{
				// Value is empty; show default
				echo apply_filters( 'the_content', $tab_post->post_content );
			}

		}
		return;

	}

	public function custom_post_types(){

		$labels = array(
				'name'               => _x( 'Tabs', 'post type general name', 'woocommerce-product-tabs' ),
				'singular_name'      => _x( 'Tab', 'post type singular name', 'woocommerce-product-tabs' ),
				'menu_name'          => _x( 'Product Tabs', 'admin menu', 'woocommerce-product-tabs' ),
				'name_admin_bar'     => _x( 'Tab', 'add new on admin bar', 'woocommerce-product-tabs' ),
				'add_new'            => _x( 'Add New', WOOCOMMERCE_PRODUCT_TABS_POST_TYPE_TAB, 'woocommerce-product-tabs' ),
				'add_new_item'       => __( 'Add New Tab', 'woocommerce-product-tabs' ),
				'new_item'           => __( 'New Tab', 'woocommerce-product-tabs' ),
				'edit_item'          => __( 'Edit Tab', 'woocommerce-product-tabs' ),
				'view_item'          => __( 'View Tab', 'woocommerce-product-tabs' ),
				'all_items'          => __( 'Product Tabs', 'woocommerce-product-tabs' ),
				'search_items'       => __( 'Search Tabs', 'woocommerce-product-tabs' ),
				'parent_item_colon'  => __( 'Parent Tabs:', 'woocommerce-product-tabs' ),
				'not_found'          => __( 'No tabs found.', 'woocommerce-product-tabs' ),
				'not_found_in_trash' => __( 'No tabs found in Trash.', 'woocommerce-product-tabs' )
			);

			$args = array(
				'labels'             => $labels,
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => false,
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'show_in_menu'       => 'woocommerce',
				'supports'           => array( 'title', 'editor' )
			);

			register_post_type( WOOCOMMERCE_PRODUCT_TABS_POST_TYPE_TAB, $args );


	}

}
