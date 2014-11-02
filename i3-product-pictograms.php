<?php
/*
Plugin Name: i3 Product Pictograms
Plugin URI: -
Description: Add Product-Feature-Pictograms
Author: Mo
Version: 0.1
Author URI: -
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * TABLE OF CONTENT
 *
 * 1 - Define language files
 * 2 - Add custom image-size
 * 3 - Include files
 * 4 - Register custom taxonomy
 * 5 - Frontpage template function
 * 6 - Create template-tag to use in theme-files
 * 7 - Create shortcode to use in single products
 *
 **/

// let's start by enqueuing our styles correctly
function i3_pp_admin_styles() {
    wp_register_style( 'i3_pp_admin_stylesheet', plugins_url( '/library/css/style.css', __FILE__ ) );
    wp_enqueue_style( 'i3_pp_admin_stylesheet' );
}
add_action( 'admin_enqueue_scripts', 'i3_pp_admin_styles' );

/**
 * 1 - Define language files
 **/

/**
 * 2 - Add custom image-size
 **/
add_image_size( 'i3-product-pictogram', 60, 60, true );

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
if ( ! function_exists( 'i3_features_taxonomy' ) ) {

// Register Custom Taxonomy
function i3_features_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Product Features', 'Taxonomy General Name', 'i3_features_plugin' ),
		'singular_name'              => _x( 'Product Feature', 'Taxonomy Singular Name', 'i3_features_plugin' ),
		'menu_name'                  => __( 'Product Features', 'i3_features_plugin' ),
		'all_items'                  => __( 'All Items', 'i3_features_plugin' ),
		'parent_item'                => __( 'Parent Item', 'i3_features_plugin' ),
		'parent_item_colon'          => __( 'Parent Item:', 'i3_features_plugin' ),
		'new_item_name'              => __( 'New Item Name', 'i3_features_plugin' ),
		'add_new_item'               => __( 'Add New Item', 'i3_features_plugin' ),
		'edit_item'                  => __( 'Edit Item', 'i3_features_plugin' ),
		'update_item'                => __( 'Update Item', 'i3_features_plugin' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'i3_features_plugin' ),
		'search_items'               => __( 'Search Items', 'i3_features_plugin' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'i3_features_plugin' ),
		'choose_from_most_used'      => __( 'Choose from the most used items', 'i3_features_plugin' ),
		'not_found'                  => __( 'Not Found', 'i3_features_plugin' ),
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
	register_taxonomy( 'i3_product_features', array( 'post' ), $args );

}

// Hook into the 'init' action
add_action( 'init', 'i3_features_taxonomy', 0 );

}


/**
 * 5 - Frontpage template function
 **/
function i3_product_pictograms_template(){
 
	$terms = get_the_terms( $post->ID, 'i3_product_features' ); 

	echo '<ul class="i3-pictograms">';

	foreach ($terms as $term) { 

 		$meta1 = get_option('option_name');
		if (empty($meta1)) $meta1 = array();
		if (!is_array($meta1)) $meta1 = (array) $meta1;
		$meta1 = isset($meta1[$term->term_id]) ? $meta1[$term->term_id] : array();
		$images = $meta1['image-field'];

		if (empty($images)) { // if there is no image display this

			$img = '<li class="i3-pictogram" style="display:none;"> No image '.$term->name.' </li>';

		} else { // if there is an image, display it 

			foreach ($images as $att) {

				$src = wp_get_attachment_image_src($att, 'i3-product-pictogram');
				$src = $src[0];

				// show image
				$img = '<li class="i3-pictogram" ><img src="'.$src.'" title="'.$term->name.'"/></li>';

			}
	
		} // end if empty images 

		echo $img;

	}// end foreach term

	echo '</ul>';
 
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
