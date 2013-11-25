<?php
/**
Warning: Missing argument 1 for Recipe_Feature_Image_Tab::media_upload_feature_form() in /srv/www/cookography.com/public_html/wp-content/plugins/recipe-schema/inc/feature-image-tab.php on line 175
Warning: Missing argument 2 for Recipe_Feature_Image_Tab::recipe_get_media_items(), called in /srv/www/cookography.com/public_html/wp-content/plugins/recipe-schema/inc/feature-image-tab.php on line 204 and defined in /srv/www/cookography.com/public_html/wp-content/plugins/recipe-schema/inc/feature-image-tab.php on line 65

 * This class provides a custom Tab in the Media popup window
 * It allows for you to set the featured image for a recipe to one of the images in a post it is attached to
 */
class Recipe_Feature_Image_Tab {


/**
 * [__construct description]
 */
public function __construct()
{

}

 	 function get_media_item_args($args) {  
 	 			if (isset($_REQUEST['target_id'])) {
 	        $args['send'] = false;
 	        }
 	         return $args;
 	    }
 
 /**
  * This filter removes the standrd tabs and adds this custom one
  * @param  array $tabs The list of tabs
  * @return array       The updated list if tabs
  */
 	function media_upload_tabs_filter($tabs) {
 				if (isset($_REQUEST['target_id'])) {
 	        unset($tabs["type_url"]);
 	        unset($tabs['library']);
 	        unset($tabs['type']);
 	        unset($tabs['gallery']);
			
 	        $newtab = array('feature_image_tab' => __('Feature Image','feature_image'));
 	        return array_merge($tabs,$newtab);
 				}
 	        else return $tabs;
 	}
 	
 	
 	/**
 	 * Draws the HTML Form in an iframe, ties into the feature image action
 	 * @return [type] Returns the HTML
 	 */
 	function media_upload_feature_tab() {
 	    return wp_iframe(array($this,'media_upload_feature_form') );
 	}
 	
 	
 	/**
 	 * Retrieve HTML for media items of post gallery.
 	 *
 	 * The HTML markup retrieved will be created for the progress of SWF Upload
 	 * component. Will also create link for showing and hiding the form to modify
 	 * the image attachment.
 	 *
 	 * @since 2.5.0
 	 *
 	 * @param int $post_id Optional. Post ID.
 	 * @param array $errors Errors for attachment, if any.
 	 * @return string
 	 */
 	function recipe_get_media_items( $post_id, $errors ) {
 		$attachments = array();
 		if ( $post_id ) {
 			$post = get_post($post_id);
 			if ( $post && $post->post_type == 'attachment' )
 				$attachments = array($post->ID => $post);
 			else
 				$attachments = get_children( array( 'post_parent' => $post_id, 'post_type' => 'attachment', 'orderby' => 'menu_order ASC, ID', 'order' => 'DESC') );
 		} else {
 			if ( is_array($GLOBALS['wp_the_query']->posts) )
 				foreach ( $GLOBALS['wp_the_query']->posts as $attachment )
 					$attachments[$attachment->ID] = $attachment;
 		}
 	
 		$output = '';
 		foreach ( (array) $attachments as $id => $attachment ) {
 			if ( $attachment->post_status == 'trash' )
 				continue;
 			if ( $item = $this->recipe_get_media_item( $id, array( 'errors' => isset($errors[$id]) ? $errors[$id] : null) ) )
 				$output .= "\n<div id='media-item-$id' class='media-item child-of-$attachment->post_parent preloaded'><div class='progress hidden'><div class='bar'></div></div><div id='media-upload-error-$id' class='hidden'></div><div class='filename hidden'></div>$item\n</div>";
 		}
 	
 		return $output;
 	}
 	
 	/**
 	 * Retrieve HTML form for modifying the image attachment.
 	 *
 	 * @since 2.5.0
 	 *
 	 * @param int $attachment_id Attachment ID for modification.
 	 * @param string|array $args Optional. Override defaults.
 	 * @return string HTML form for attachment.
 	 */
 	function recipe_get_media_item( $attachment_id, $recipe_id) {
 	global $redir_tab;
 	
 		if ( ( $attachment_id = intval( $attachment_id ) ) && $thumb_url = wp_get_attachment_image_src( $attachment_id, 'thumbnail', true ) )
 			$thumb_url = $thumb_url[0];
 		else
 			$thumb_url = false;
 	
 		$post = get_post( $attachment_id );
 		$current_post_id = !empty( $_GET['post_id'] ) ? (int) $_GET['post_id'] : 0;
		$target_post_id = !empty( $_GET['target_id'] ) ? (int) $_GET['target_id'] : 0;
		

 		$filename = esc_html( basename( $post->guid ) );
 		$title = esc_attr( $post->post_title );
 	 	
 		$post_mime_types = get_post_mime_types();
 		$keys = array_keys( wp_match_mime_types( array_keys( $post_mime_types ), $post->post_mime_type ) );
 		$type = array_shift( $keys );
 		$type_html = "<input type='hidden' id='type-of-$attachment_id' value='" . esc_attr( $type ) . "' />";
 	
		$display_title = ( !empty( $title ) ) ? $title : $filename; // $title shouldn't ever be empty, but just in case
 		$display_title =  "<div class='filename new'><span class='title'>" . wp_html_excerpt( $display_title, 60 ) . "</span></div>";
 	

 	
 		$attachment_url = get_permalink( $attachment_id );
 	
 	$thumbnail = '';
 	$calling_post_id = 0;
 	if ( isset( $_GET['post_id'] ) )
 		$calling_post_id = absint( $_GET['post_id'] );
 	elseif ( isset( $_POST ) && count( $_POST ) ) // Like for async-upload where $_GET['post_id'] isn't set
 		$calling_post_id = $post->post_parent;
 	if ( 'image' == $type && $calling_post_id && current_theme_supports( 'post-thumbnails', get_post_type( $calling_post_id ) )
 		&& post_type_supports( get_post_type( $calling_post_id ), 'thumbnail' ) && get_post_thumbnail_id( $calling_post_id ) != $attachment_id ) {
 		$ajax_nonce = wp_create_nonce( "set_post_thumbnail-$target_post_id" );
 		$thumbnail = "<a class='wp-post-thumbnail' id='wp-post-thumbnail-" . $attachment_id . "' href='#' onclick='RecipeSetAsThumbnail(\"$attachment_id\", \"$target_post_id\", \"$ajax_nonce\");return false;'>" . esc_html__( "Use as featured image" ) . "</a>";
 	}
 		$item = "
 		$type_html


 		$display_title
 		<table class='describe'>
 			<thead class='media-item-info' id='media-head-$post->ID'>
 			<tr valign='top'>
 				<td class='A1B1' id='thumbnail-head-$post->ID'>
 				<p><a href='$attachment_url' target='_blank'><img class='thumbnail' src='$thumb_url' alt='' /></a></p>
 		 		</td><td>$thumbnail</td></tr>\n";
 	
 	
 	
 		$item .= "
 			</thead>
 			<tbody>
 			<tr><td colspan='2' class='imgedit-response' id='imgedit-response-$post->ID'></td></tr>
 			<tr><td style='display:none' colspan='2' class='image-editor' id='image-editor-$post->ID'></td></tr>\n";
 	
 	 	
 		
 	

 		$item .= "\t</tbody>\n";
 		$item .= "\t</table>\n";
 	
 		
 		if ( $post->post_parent < 1 && isset( $_REQUEST['post_id'] ) ) {
 			$parent = (int) $_REQUEST['post_id'];
 			$parent_name = "attachments[$attachment_id][post_parent]";
 			$item .= "\t<input type='hidden' name='$parent_name' id='$parent_name' value='$parent' />\n";
 		}
 	
 		return $item; 	}
 	
 	
 	function media_upload_feature_form($errors = null) {
 	    	global $redir_tab, $type;
 	    
 	    	$redir_tab = 'insertgmap';
 	    	media_upload_header();
 	    
 	    	$post_id = intval($_REQUEST['post_id']);
 	    	$form_action_url = admin_url("media-upload.php?type=$type&tab=gallery&post_id=$post_id");
 	    	$form_action_url = apply_filters('media_upload_form_url', $form_action_url, $type);
 	    	$form_class = 'media-upload-form validate';
 	    
 	    	if ( get_user_setting('uploader') )
 	    		$form_class .= ' html-uploader';
 	    ?>
 	    
 	   
 	
 	    <form enctype="multipart/form-data" method="post" action="<?php echo esc_attr($form_action_url); ?>" class="<?php echo $form_class; ?>" id="gallery-form">
 	    <?php wp_nonce_field('media-form'); ?>
 	    <?php //media_upload_form( $errors ); ?>
 	    <table class="widefat" cellspacing="0">
 	    <thead><tr>
 	    <th>Photo</th>
 	    <th class="order-head"></th>
 	    <th class="actions-head"><?php _e('Actions'); ?></th>
 	    </tr></thead>
 	    </table>
 	    <div id="media-items">
 	    <?php add_filter('attachment_fields_to_edit', 'media_post_single_attachment_fields_to_edit', 10, 2); ?>
 	    <?php echo $this->recipe_get_media_items($post_id, null); ?>
 	    </div>
 	    
 	    <p class="ml-submit">
 	    <?php submit_button( __( 'Save all changes' ), 'button savebutton', 'save', false, array( 'id' => 'save-all', 'style' => 'display: none;' ) ); ?>
 	    <input type="hidden" name="post_id" id="post_id" value="<?php echo (int) $post_id; ?>" />
 	    <input type="hidden" name="type" value="<?php echo esc_attr( $GLOBALS['type'] ); ?>" />
 	    <input type="hidden" name="tab" value="<?php echo esc_attr( $GLOBALS['tab'] ); ?>" />
 	    </p>
 	    
 	    </form>
 	    <?php
 	    }
 	    



}


?>