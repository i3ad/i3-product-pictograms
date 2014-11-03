<?php
/**
 * TABLE OF CONTENT
 * 
 * 1 - Register taxonomy meta-box
 * 2 - Create post-type metabox
 *
 */

/**
 * 1 - Register taxonomy meta-box
 */
function i3_ppicto_register_taxonomy_meta_boxes()
{
	// Make sure there's no errors when the plugin is deactivated or during upgrade
	if ( !class_exists( 'RW_Taxonomy_Meta' ) )
		return;

	$meta_sections = array();

	// First meta section
	$meta_sections[] = array(
		'taxonomies' => array('i3_product_features'),  // list of taxonomies. Default is array('category', 'post_tag'). Optional
		'id'         => 'option_name',                 // ID of each section, will be the option name
		'fields' => array(                             // List of meta fields
			// IMAGE
			array(
				'name' => __('Pictogram', 'i3pp-plugin'),
				'id'   => 'image-field',
				'type' => 'image',
			),
		),
	);

	foreach ( $meta_sections as $meta_section )
	{
		new RW_Taxonomy_Meta( $meta_section );
	}
}
add_action( 'admin_init', 'i3_ppicto_register_taxonomy_meta_boxes' );


/**
 * 2 - Create post-type metabox
 *
 * Base from Stephen Harris
 * - https://github.com/stephenh1988/Radio-Buttons-for-Taxonomies
 *
 * Modified to use checkboxes instead of radio-boxes, with help of
 * - https://wordpress.org/support/topic/display-tag-admin-box-like-categories-without-hierarchy
 **/

class i3_Checkbox_Taxonomy {
	static $taxonomy = 'i3_product_features';
	static $taxonomy_metabox_id = 'i3_product_featuresdiv';
	static $post_type= 'product'; //Change the post-type here

	public function load(){
		//Remove old taxonomy meta box  
		add_action( 'admin_menu', array(__CLASS__,'remove_meta_box'));  

		//Add new taxonomy meta box  
		add_action( 'add_meta_boxes', array(__CLASS__,'add_meta_box'));  

	}


	public static function remove_meta_box(){  
   		remove_meta_box(self::$taxonomy_metabox_id, self::$post_type, 'normal');  
	} 


	public function add_meta_box() {  
		add_meta_box( 'i3_product_features_id', __('Product Pictograms','i3pp-plugin'),array(__CLASS__,'metabox'), self::$post_type ,'normal','core');  
	}  
        

	//Callback to set up the metabox  
	public static function metabox( $post ) {  
		//Get taxonomy and terms  
       	 $taxonomy = self::$taxonomy;
      
       	 //Set up the taxonomy object and get terms  
       	 $tax = get_taxonomy($taxonomy);  
       	 $terms = get_terms($taxonomy,array('hide_empty' => 0));  
      
       	 //Name of the form  
       	 $name = 'tax_input[' . $taxonomy . '][]';  // Added []
      
       	 //Get current terms  
       	 $postterms = get_the_terms( $post->ID,$taxonomy );  

		//Make an array of the ids of all terms attached to the post
        $array_post_term_ids = array();
        if ($postterms) {
            foreach ($postterms as $post_term) {
                $post_term_id = $post_term->term_id;
                $array_post_term_ids[] = $post_term_id;
            }
        }
		?>  

       	 <?php #print_r($array_post_term_ids); // just to control savestate ?>
      
		<div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">

			<!-- Display taxonomy terms -->
			<div id="<?php echo $taxonomy; ?>-all" class="">
				<ul id="<?php echo $taxonomy; ?>checklist" class="list:<?php echo $taxonomy?> categorychecklist form-no-clear">
				
				<?php foreach($terms as $term){

					//Start get the taxonomy image 
					$meta = get_option('option_name');
					if (empty($meta)) $meta = array();
					if (!is_array($meta)) $meta = (array) $meta;
					$meta = isset($meta[$term->term_id]) ? $meta[$term->term_id] : array();
					$images = $meta['image-field'];

					if (empty($images)) { // If there is no image, display this text

						echo '<li class="no-picto"><strong>'.__($term->name).'</strong> '.__('has no image.', 'i3pp-plugin').'</li>';

					} else { // If there is an image, display it 

						foreach ($images as $att) {
							$src = wp_get_attachment_image_src($att, 'i3-product-pictogram');
							$src = $src[0];

							$src = '<img src="'.$src.'" title="'.__($term->name).'" alt="'.__($term->name).'"/>';
						}

						//Define checked state
						if (in_array($term->term_id, $array_post_term_ids)) {
	                        $checked = "checked = ''";
	                        $is_checked = "checked";
	                    }
	                    else {
	                        $checked = "";
	                        $is_checked = "";
	                    }

	                    //Display the list-item
	       				$id = $taxonomy.'-'.$term->term_id;
					    	echo "<li id='$id' class='$is_checked'><label class='selectit'>";
					        echo $src;
					        echo "<input type='checkbox' id='in-$id' name='{$name}' {$checked} value='{$term->term_id}' />".__($term->name)."<br />";
					        echo "</label></li>";
						
					} //End if images is empty
					//End get the taxonomy image

		       	 }?>

				</ul>
			</div><!-- End -->


		</div><!-- End .categorydiv -->
        <?php  
    }
}
i3_Checkbox_Taxonomy::load();