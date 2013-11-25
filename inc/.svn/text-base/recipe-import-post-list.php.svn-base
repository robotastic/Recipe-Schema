<?php


if (!class_exists('WP_List_Table')) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Recipe_Schema_Import_Post_List_Table extends WP_List_Table {

function __construct() {
	
			global $post_type_object, $wpdb, $wp_query;
			
			add_query_arg('post_type', 'post');
			$post_type = 'post';//get_current_screen()->post_type;
			$post_type_object = get_post_type_object( $post_type );

	
			if ( !current_user_can( $post_type_object->cap->edit_others_posts ) ) {
				$this->user_posts_count = $wpdb->get_var( $wpdb->prepare( "
					SELECT COUNT( 1 ) FROM $wpdb->posts
					WHERE post_type = %s AND post_status NOT IN ( 'trash', 'auto-draft' )
					AND post_author = %d
				", $post_type, get_current_user_id() ) );
	
				if ( $this->user_posts_count && empty( $_REQUEST['post_status'] ) && empty( $_REQUEST['all_posts'] ) && empty( $_REQUEST['author'] ) && empty( $_REQUEST['show_sticky'] ) )
					$_GET['author'] = get_current_user_id();
			}
	
			if ( 'post' == $post_type && $sticky_posts = get_option( 'sticky_posts' ) ) {
				$sticky_posts = implode( ', ', array_map( 'absint', (array) $sticky_posts ) );
				$this->sticky_posts_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT( 1 ) FROM $wpdb->posts WHERE post_type = %s AND post_status != 'trash' AND ID IN ($sticky_posts)", $post_type ) );
			}
			if (is_admin()) {
				add_action( 'pre_get_posts', array($this, 'import_pre_get_posts') );
			}
	
	parent::__construct( array(
		'singular' 	=> 'post',
		'plural' 		=> 'posts',
		'ajax' 			=> false
	) );
	
}
 	function import_pre_get_posts( &$wp_query )
 	{

 	    if ( is_admin() && array_key_exists( 'page', $_GET ) && ($_GET['page'] == 'import_post') ) {
 	        $wp_query->set( 'post_type', 'post');
 	    }
 	}
 	
function column_default($item, $column_name) {
	switch($column_name) {
		case 'title':
		case 'categories':
		case 'date':
			return $item[$column_name];
		default:
			return print_r($item, true);	
	}
}

function column_title($item) {

	$actions = array(
		'import' => sprintf('<a href="?page=%s&action=%s&post=%s">Import Recipe</a>',$_REQUEST['page'],'import',$item['ID'])
		);
		
	return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
		$item['title'],
		$item['ID'],
		$this->row_actions($actions)
	);
}

function get_columns() {
	$columns = array(
		'title' => 'Title',
		'categories' => 'Categories',
		'date' => 'Date'
	);
	return $columns;
}

function prepare_items() {
	global $post_type_object, $avail_post_stati, $wp_query, $per_page, $mode;
	
	$avail_post_stati = wp_edit_posts_query();
	
	$this->hierarchical_display = false;
	
	$total_items = $this->hierarchical_display ? $wp_query->post_count : $wp_query->found_posts;

	$post_type = 'post'; //$post_type_object->name;
	$per_page = $this->get_items_per_page( 'edit_' . $post_type . '_per_page' );
	$per_page = apply_filters( 'edit_posts_per_page', $per_page, $post_type );
	$total_pages = $wp_query->max_num_pages;

	$mode = empty( $_REQUEST['mode'] ) ? 'list' : $_REQUEST['mode'];

	$this->is_trash = isset( $_REQUEST['post_status'] ) && $_REQUEST['post_status'] == 'trash';

	
	$this->set_pagination_args( array(
		'total_items' => $total_items,
		'total_pages' => $total_pages,
		'per_page' => $per_page
	) );
	
	$columns = $this->get_columns();
	$hidden = array();
	$sortable = array();
	
	
	$this->_column_headers = array($columns, $hidden, $sortable);
	
	$current_page = $this->get_pagenum();
	
}


	function extra_tablenav( $which ) {
		global $post_type_object, $cat;
?>
		<div class="alignleft actions">
<?php
		if ( 'top' == $which && !is_singular() ) {

			$this->months_dropdown( $post_type_object->name );

			if ( is_object_in_taxonomy( $post_type_object->name, 'category' ) ) {
				$dropdown_options = array(
					'show_option_all' => __( 'View all categories' ),
					'hide_empty' => 0,
					'hierarchical' => 1,
					'show_count' => 0,
					'orderby' => 'name',
					'selected' => $cat
				);
				wp_dropdown_categories( $dropdown_options );
			}
			?>
			&nbsp;
			<input type="checkbox" name="no_recipes" <?php if (isset($_GET["no_recipes"])) echo "checked='checked'"; ?>/>  Hide Posts With Recipes  &nbsp;
			<?php
			do_action( 'restrict_manage_posts' );
			submit_button( __( 'Filter' ), 'secondary', false, false, array( 'id' => 'post-query-submit' ) );
		}

		if ( $this->is_trash && current_user_can( $post_type_object->cap->edit_others_posts ) ) {
			submit_button( __( 'Empty Trash' ), 'button-secondary apply', 'delete_all', false );
		}
?>
		</div>
<?php
	}
	
		function pagination( $which ) {
			global $post_type_object, $mode;
	
			parent::pagination( $which );
	
			if ( 'top' == $which && !$post_type_object->hierarchical )
				$this->view_switcher( $mode );
		}
	
		function get_table_classes() {
			global $post_type_object;
	
			return array( 'widefat', 'fixed', $post_type_object->hierarchical ? 'pages' : 'posts' );
		}

function display_rows( $posts = array() ) {
		global $wp_query, $post_type_object, $per_page;

	
	
		
		if ( empty( $posts ) )
			$posts = $wp_query->posts;

		add_filter( 'the_title', 'esc_html' );


		$this->_display_rows( $posts );
	}

	function _display_rows( $posts ) {
		global $post, $mode;


		

		// Create array of post IDs.
		$post_ids = array();

		foreach ( $posts as $a_post )
			$post_ids[] = $a_post->ID;

		$this->comment_pending_count = get_pending_comments_num( $post_ids );

		foreach ( $posts as $post )
			$this->single_row( $post );
	}
	function has_items() {
		return have_posts();
	}

	function no_items() {
		global $post_type_object;

		if ( isset( $_REQUEST['post_status'] ) && 'trash' == $_REQUEST['post_status'] )
			echo $post_type_object->labels->not_found_in_trash;
		else
			echo $post_type_object->labels->not_found;
	}
	
	function display_tablenav( $which ) {
		//if ( 'top' == $which )
			//wp_nonce_field( 'bulk-' . $this->_args['plural'] );
?>
	<div class="tablenav <?php echo esc_attr( $which ); ?>">

		<div class="alignleft actions">
			<?php $this->bulk_actions( $which ); ?>
		</div>
<?php
		$this->extra_tablenav( $which );
		$this->pagination( $which );
?>

		<br class="clear" />
	</div>
<?php
	}
	
	function single_row( $a_post, $level = 0 ) {
			global $post, $mode;
			static $alternate;
	
			
			$global_post = $post;
			$post = $a_post;
			setup_postdata( $post );
	
			$import_link = site_url() . '/wp-admin/edit.php?post_type=' . RECIPE_SCHEMA_POSTTYPE . '&page=import_post&action=post_selected&post_id=' .  $post->ID; //get_edit_post_link( $post->ID );
			$title = _draft_or_post_title();
			$post_type_object = get_post_type_object( $post->post_type );
			$can_edit_post = current_user_can( $post_type_object->cap->edit_post, $post->ID );
	
			$alternate = 'alternate' == $alternate ? '' : 'alternate';
			$classes = $alternate . ' iedit author-' . ( get_current_user_id() == $post->post_author ? 'self' : 'other' );
		?>
			<tr id="post-<?php echo $post->ID; ?>" class="<?php echo implode( ' ', get_post_class( $classes, $post->ID ) ); ?>" valign="top">
		<?php
	
			list( $columns, $hidden ) = $this->get_column_info();
	
			foreach ( $columns as $column_name => $column_display_name ) {
				$class = "class=\"$column_name column-$column_name\"";
	
				$style = '';
				if ( in_array( $column_name, $hidden ) )
					$style = ' style="display:none;"';
	
				$attributes = "$class$style";
	
				switch ( $column_name ) {
	
				case 'cb':
				?>
				<th scope="row" class="check-column"><?php if ( $can_edit_post ) { ?><input type="checkbox" name="post[]" value="<?php the_ID(); ?>" /><?php } ?></th>
				<?php
				break;
	
				case 'title':
					if ( $this->hierarchical_display ) {
						$attributes = 'class="post-title page-title column-title"' . $style;
	
						if ( 0 == $level && (int) $post->post_parent > 0 ) {
							//sent level 0 by accident, by default, or because we don't know the actual level
							$find_main_page = (int) $post->post_parent;
							while ( $find_main_page > 0 ) {
								$parent = get_page( $find_main_page );
	
								if ( is_null( $parent ) )
									break;
	
								$level++;
								$find_main_page = (int) $parent->post_parent;
	
								if ( !isset( $parent_name ) )
									$parent_name = apply_filters( 'the_title', $parent->post_title, $parent->ID );
							}
						}
	
						$pad = str_repeat( '&#8212; ', $level );
	?>
				<td <?php echo $attributes ?>><strong><?php if ( $can_edit_post && $post->post_status != 'trash' ) { ?><a class="row-title" href="<?php echo $edit_link; ?>" title="<?php echo esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $title ) ); ?>"><?php echo $pad; echo $title ?></a><?php } else { echo $pad; echo $title; }; _post_states( $post ); echo isset( $parent_name ) ? ' | ' . $post_type_object->labels->parent_item_colon . ' ' . esc_html( $parent_name ) : ''; ?></strong>
	<?php
					}
					else {
						$attributes = 'class="post-title page-title column-title"' . $style;
	?>
				<td <?php echo $attributes ?>><strong><?php if ( $can_edit_post && $post->post_status != 'trash' ) { ?><a class="row-title" href="<?php echo $import_link; ?>" title="<?php echo esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $title ) ); ?>"><?php echo $title ?></a><?php } else { echo $title; }; _post_states( $post ); ?></strong>
	<?php
						if ( 'excerpt' == $mode ) {
							the_excerpt();
						}
					}
	
					$actions = array();
					if ( $can_edit_post && 'trash' != $post->post_status ) {
						$actions['import'] = '<a href="' . $import_link . '" title="' . esc_attr( __( 'Import recipe from this post' ) ) . '">' . __( 'Import recipe from this post' ) . '</a>';
					}
					if ( $post_type_object->public ) {
						if ( in_array( $post->post_status, array( 'pending', 'draft', 'future' ) ) ) {
							if ( $can_edit_post )
								$actions['view'] = '<a href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) . '" title="' . esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink">' . __( 'Preview' ) . '</a>';
						} elseif ( 'trash' != $post->post_status ) {
							$actions['view'] = '<a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink">' . __( 'View' ) . '</a>';
						}
					}
	
					$actions = apply_filters( is_post_type_hierarchical( $post->post_type ) ? 'page_row_actions' : 'post_row_actions', $actions, $post );
					echo $this->row_actions( $actions );
	
					get_inline_data( $post );
					echo '</td>';
				break;
	
				case 'date':
					if ( '0000-00-00 00:00:00' == $post->post_date && 'date' == $column_name ) {
						$t_time = $h_time = __( 'Unpublished' );
						$time_diff = 0;
					} else {
						$t_time = get_the_time( __( 'Y/m/d g:i:s A' ) );
						$m_time = $post->post_date;
						$time = get_post_time( 'G', true, $post );
	
						$time_diff = time() - $time;
	
						if ( $time_diff > 0 && $time_diff < 24*60*60 )
							$h_time = sprintf( __( '%s ago' ), human_time_diff( $time ) );
						else
							$h_time = mysql2date( __( 'Y/m/d' ), $m_time );
					}
	
					echo '<td ' . $attributes . '>';
					if ( 'excerpt' == $mode )
						echo apply_filters( 'post_date_column_time', $t_time, $post, $column_name, $mode );
					else
						echo '<abbr title="' . $t_time . '">' . apply_filters( 'post_date_column_time', $h_time, $post, $column_name, $mode ) . '</abbr>';
					echo '<br />';
					if ( 'publish' == $post->post_status ) {
						_e( 'Published' );
					} elseif ( 'future' == $post->post_status ) {
						if ( $time_diff > 0 )
							echo '<strong class="attention">' . __( 'Missed schedule' ) . '</strong>';
						else
							_e( 'Scheduled' );
					} else {
						_e( 'Last Modified' );
					}
					echo '</td>';
				break;
	
				case 'categories':
				?>
				<td <?php echo $attributes ?>><?php
					$categories = get_the_category();
					if ( !empty( $categories ) ) {
						$out = array();
						foreach ( $categories as $c ) {
							$out[] = sprintf( '<a href="%s">%s</a>',
								esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'category_name' => $c->slug ), 'edit.php' ) ),
								esc_html( sanitize_term_field( 'name', $c->name, $c->term_id, 'category', 'display' ) )
							);
						}
						echo join( ', ', $out );
					} else {
						_e( 'Uncategorized' );
					}
				?></td>
				<?php
				break;
	
				case 'tags':
				?>
				<td <?php echo $attributes ?>><?php
					$tags = get_the_tags( $post->ID );
					if ( !empty( $tags ) ) {
						$out = array();
						foreach ( $tags as $c ) {
							$out[] = sprintf( '<a href="%s">%s</a>',
								esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'tag' => $c->slug ), 'edit.php' ) ),
								esc_html( sanitize_term_field( 'name', $c->name, $c->term_id, 'tag', 'display' ) )
							);
						}
						echo join( ', ', $out );
					} else {
						_e( 'No Tags' );
					}
				?></td>
				<?php
				break;
	
				case 'comments':
				?>
				<td <?php echo $attributes ?>><div class="post-com-count-wrapper">
				<?php
					$pending_comments = isset( $this->comment_pending_count[$post->ID] ) ? $this->comment_pending_count[$post->ID] : 0;
	
					$this->comments_bubble( $post->ID, $pending_comments );
				?>
				</div></td>
				<?php
				break;
	
				case 'author':
				?>
				<td <?php echo $attributes ?>><?php
					printf( '<a href="%s">%s</a>',
						esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'author' => get_the_author_meta( 'ID' ) ), 'edit.php' )),
						get_the_author()
					);
				?></td>
				<?php
				break;
	
				default:
				?>
				<td <?php echo $attributes ?>><?php
					if ( is_post_type_hierarchical( $post->post_type ) )
						do_action( 'manage_pages_custom_column', $column_name, $post->ID );
					else
						do_action( 'manage_posts_custom_column', $column_name, $post->ID );
					do_action( "manage_{$post->post_type}_posts_custom_column", $column_name, $post->ID );
				?></td>
				<?php
				break;
			}
		}
		?>
			</tr>
		<?php
			$post = $global_post;
		}
	

}