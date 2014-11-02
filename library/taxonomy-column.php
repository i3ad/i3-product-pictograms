<?php
/**
 * TABLE OF CONTENT
 * 
 * 1 - Custom column header
 * 2 - Custom column content
 *
 */


/**
 * 1 - Custom column header
 **/
function i3pg_column_header($column) {

    $column['pictogram'] = __('Pictogram', 'my_plugin');
    return $column;

}
add_filter('manage_edit-i3_product_features_columns', 'i3pg_column_header', 10, 1);

/**
 * 2 - Custom column content
 **/
function i3pg_column_content($deprecated,$column_name,$term_id){
    
    if ($column_name == 'pictogram') {

    	$t_id = $term_id;

 		$meta = get_option('option_name');
		if (empty($meta)) $meta = array();
		if (!is_array($meta)) $meta = (array) $meta;
		$meta = isset($meta[$t_id]) ? $meta[$t_id] : array();
		$images = $meta['image-field'];

		if (empty($images)) { // If there is no image, display this text

				$column_content = '<span style="background:red;color:white;"> No image </span><br><a href="'.get_edit_term_link( $t_id, 'i3_product_features', 'post' ).'" class="button">Add image</a>';

		} else { // If there is an image, display it now 

			foreach ($images as $att) {

				$src = wp_get_attachment_image_src($att, 'i3-product-pictogram');
				$src = $src[0];
				$term_edit_link = get_edit_term_link( $t_id, 'i3-product-features', 'post' );

				// show image
				$column_content = '<a href="'.get_edit_term_link( $t_id, 'i3_product_features', 'post' ).'" title="Edit"><img src="'.$src.'" alt="'.$term->name.'"/></a>';

			}

		} // End if images is empty

		echo $column_content;
		
    } // End if column-name is pictogram

}
add_action('manage_i3_product_features_custom_column','i3pg_column_content',10,3);