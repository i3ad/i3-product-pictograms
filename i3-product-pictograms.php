<?php
/*
Plugin Name: i3 Product Pictograms
Plugin URI: -
Description: Create and add custom pictograms to your WooCommerce products.
Author: Mo
Version: 1.3
Author URI: -
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * TABLE OF CONTENT
 *
 * 1 - Init function
 * 2 - Register scripts
 * 3 - Include files
 * 4 - Register custom taxonomy
 * 5 - Frontpage template function
 * 6 - Create template-tag to use in theme-files
 * 7 - Create shortcode to use in single products
 *
 **/


/**
 * 1 - Init function
 **/
function i3pp_init_method() {

	//Load textdomain
    load_plugin_textdomain('i3pp-plugin', false, basename( dirname( __FILE__ ) ) . '/languages' );
    
    //Add custom image-size
    add_image_size( 'i3-product-pictogram', 60, 60, true );
}
add_action('init', 'i3pp_init_method');

/**
 * 2 - Register scripts
 **/
function i3pp_admin_styles() {
    wp_register_style( 'i3pp_admin_stylesheet', plugins_url( '/library/css/admin-style.css', __FILE__ ) );
    wp_enqueue_style( 'i3pp_admin_stylesheet' );
}
add_action( 'admin_enqueue_scripts', 'i3pp_admin_styles' );

function i3pp_front_styles() {
    if ( ! is_admin() ) {
        wp_enqueue_style( 'i3pp-styles', plugins_url('/library/css/style.css', __FILE__) );

        wp_enqueue_script( 'i3pp-tooltip', plugins_url('/library/js/tooltip.js', __FILE__) );
    }    
}
add_action('wp_enqueue_scripts', 'i3pp_front_styles');

/**
 * 3 - Include files
 **/
//Include taxonomy meta class (no need to edit)
include_once( dirname( __FILE__ ) . '/library/taxonomy-class.php' );

//Include taxonomy and type meta-box
include_once( dirname( __FILE__ ) . '/library/meta-boxes.php' );

//Include taxonomy admin column functions
include_once( dirname( __FILE__ ) . '/library/taxonomy-column.php' );

/**
 * 4 - Register custom taxonomy
 **/
if ( ! function_exists( 'i3_pictograms_taxonomy' ) ) {

// Register Custom Taxonomy
function i3_pictograms_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Product Pictograms', 'Taxonomy General Name', 'i3pp-plugin' ),
		'singular_name'              => _x( 'Product Pictogram', 'Taxonomy Singular Name', 'i3pp-plugin' ),
		'menu_name'                  => __( 'Pictograms', 'i3pp-plugin' ),
		'all_items'                  => __( 'All Pictograms', 'i3pp-plugin' ),
		'parent_item'                => __( 'Parent Pictogram', 'i3pp-plugin' ),
		'parent_item_colon'          => __( 'Parent Pictogram:', 'i3pp-plugin' ),
		'new_item_name'              => __( 'New Pictogram Name', 'i3pp-plugin' ),
		'add_new_item'               => __( 'Add New Pictogram', 'i3pp-plugin' ),
		'edit_item'                  => __( 'Edit Pictogram', 'i3pp-plugin' ),
		'update_item'                => __( 'Update Pictogram', 'i3pp-plugin' ),
		'separate_items_with_commas' => __( 'Separate Pictograms with commas', 'i3pp-plugin' ),
		'search_items'               => __( 'Search Pictograms', 'i3pp-plugin' ),
		'add_or_remove_items'        => __( 'Add or remove Pictograms', 'i3pp-plugin' ),
		'choose_from_most_used'      => __( 'Choose from the most used Pictograms', 'i3pp-plugin' ),
		'not_found'                  => __( 'Not Found', 'i3pp-plugin' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => false,
		'show_in_nav_menus'          => false,
		'show_tagcloud'              => false,
	);
	register_taxonomy( 'i3_product_features', array( 'product' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'i3_pictograms_taxonomy', 0 );

}


/**
 * 5 - Frontpage template function
 **/
function i3_product_pictograms_template(){
 
	$terms = get_the_terms( $post->ID, 'i3_product_features' ); 

	if (!empty($terms)) { //Only display if there are terms

		echo '<ul class="i3-pictograms">';

		foreach ($terms as $term) { 

	 		$meta = get_option('option_name');
			if (empty($meta)) $meta = array();
			if (!is_array($meta)) $meta = (array) $meta;
			$meta = isset($meta[$term->term_id]) ? $meta[$term->term_id] : array();
			$images = $meta['image-field'];

			if (empty($images)) { // if there is no image display this

				$img = '<li class="i3-pictogram" style="display:none;">'. __('No image', 'i3pp-plugin').__($term->name).' </li>';

			} else { // if there is an image, display it 

				foreach ($images as $att) {

					$src = wp_get_attachment_image_src($att, 'i3-product-pictogram');
					$src = $src[0];

					// show image
					$img = '<li class="i3-pictogram '.$term->slug.'" ><img src="'.$src.'" title="'.__($term->name).'"/></li>';

				}
		
			} // end if empty images 

			echo $img;

		}// end foreach term

		echo '</ul>';

	}// if there are any terms
 
};// End of function


/**
 * 6 - Create template-tag to use in theme-files
 *
 * Use: <?php i3_show_pictograms(); ?>
 **/
function i3_show_pictograms(){
 
    print i3_product_pictograms_template();

};


/**
 * 7 - Create shortcode to use in single products
 *
 * Use: [i3_pictograms]
 **/
function i3_show_pictograms_shortcode($atts, $content=null){

	ob_start();
	 
		$content = i3_product_pictograms_template();
		$content = ob_get_contents();

	ob_end_clean();
	 
	return $content;
 
}
add_shortcode('i3_pictograms', 'i3_show_pictograms_shortcode');

