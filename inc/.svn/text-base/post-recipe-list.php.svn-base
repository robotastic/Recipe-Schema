<?php


if (!class_exists('WP_List_Table')) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Post_Recipe_List_Table extends WP_List_Table {

public $post_id;
public $recipe_list;

var $example_data = array(
            array(
                'ID'        => 95,
                'post_title'     => '300'
                
            ));


function __construct($post_id, $recipe_list) {
	$this->post_id = $post_id;
	$this->recipe_list = $recipe_list;		
	
	parent::__construct( array(
		'singular' 	=> 'recipe',
		'plural' 		=> 'recipes',
		'ajax' 			=> false
	) );
	
}
 	
function column_default($item, $column_name) {
	switch($column_name) {
		case 'post_title':
		case 'thumbnail':
			return $item[$column_name];
		default:
			return print_r($item, true);	
	}
}

function column_post_title($item) {

	$actions = array(
		'remove' => sprintf('<a href="#" onclick="ROBORemoveRecipe(event)">Remove Recipe</a>'),
		/*'image' => sprintf('<a href="#" onclick="ROBOLoadMediaUpload( %s, %s)" id="change_image_%s">Change Image</a>
		', $this->post_id, $item['ID'] ),
		);*/
        'image' => sprintf('<a href="#" class="change-recipe-image" id="change-image-%s">Change Image</a>
        ',  $item['ID'] ),
        );
		
	return sprintf('<strong>%1$s</strong><input type="hidden" name="recipe_id[]" value="%2$s"><input type="hidden" name="recipe_thumbnail_id[]" value="%3$s"> %4$s',
		$item['post_title'],
		$item['ID'],
        get_post_thumbnail_id( $item['ID']),
		$this->row_actions($actions)
	);
}

function column_thumbnail($item) {

return sprintf("<div class='recipe-thumb' id='media-item-thumb-%s'>%s</div>", $item['ID'], get_the_post_thumbnail($item['ID'], array(32,32))); 
}

function get_columns() {
	$columns = array(
		'post_title' => 'Title',
		'thumbnail' => 'Featured Image'
	);
	return $columns;
}

function get_hidden_columns() {
	$sortable_columns = array(
	    'post_title'     => array('post_title',false),     //true means its already sorted
	    'thumbnail'    => array('thumbnail',false),
	    'recipe_id'  => array('recipe_id',true)
	);
}

function no_items() {
	_e( 'No recipes have been added.' );
}

function display_tablenav() {
}

function prepare_items() {
    global $wpdb;
    /**
     * First, lets decide how many records per page to show
     */
    $per_page = 10;
    
    
    /**
     * REQUIRED. Now we need to define our column headers. This includes a complete
     * array of columns to be displayed (slugs & titles), a list of columns
     * to keep hidden, and a list of columns that are sortable. Each of these
     * can be defined in another method (as we've done here) before being
     * used to build the value for our _column_headers property.
     */
    $columns = $this->get_columns();
    $hidden = array();
    $sortable = array();
    
    
    /**
     * REQUIRED. Finally, we build an array to be used by the class for column 
     * headers. The $this->_column_headers property takes an array which contains
     * 3 other arrays. One for all columns, one for hidden columns, and one
     * for sortable columns.
     */
    $this->_column_headers = array($columns, $hidden, $sortable);
    
    
    
    
    /**
     * Instead of querying a database, we're going to fetch the example data
     * property we created for use in this plugin. This makes this example 
     * package slightly different than one you might build on your own. In 
     * this example, we'll be using array manipulation to sort and paginate 
     * our data. In a real-world implementation, you will probably want to 
     * use sort and pagination data to build a custom query instead, as you'll
     * be able to use your precisely-queried data immediately.
     *
     * 
     */
     
     
     	
     	if ($this->recipe_list)
     	{

     
                 $querystr = "
        SELECT wposts.post_title AS post_title, wposts.ID as ID
        FROM $wpdb->posts as wposts
        
        WHERE  wposts.post_type = '" . RECIPE_SCHEMA_POSTTYPE . "'
        AND  wposts.ID IN (" . implode(",", $this->recipe_list) . ")";
     	 $data = $wpdb->get_results($querystr, ARRAY_A);
               
          
    
            
    /**
     * REQUIRED for pagination. Let's figure out what page the user is currently 
     * looking at. We'll need this later, so you should always include it in 
     * your own package classes.
     */
    $current_page = $this->get_pagenum();
    
    /**
     * REQUIRED for pagination. Let's check how many items are in our data array. 
     * In real-world use, this would be the total number of items in your database, 
     * without filtering. We'll need this later, so you should always include it 
     * in your own package classes.
     */
    $total_items = count($data);
    
    
    /**
     * The WP_List_Table class does not handle pagination for us, so we need
     * to ensure that the data is trimmed to only the current page. We can use
     * array_slice() to 
     */
    //$data = array_slice($data,(($current_page-1)*$per_page),$per_page);
    
    
    
    /**
     * REQUIRED. Now we can add our *sorted* data to the items property, where 
     * it can be used by the rest of the class.
     */
    $this->items = $data;
    
    
    /**
     * REQUIRED. We also have to register our pagination options & calculations.
     */
     $this->set_pagination_args(array());
 
    }
}

	
		function get_table_classes() {
			global $post_type_object;
	
			return array( 'widefat', 'fixed', $post_type_object->hierarchical ? 'pages' : 'posts', 'awesome-recipe-list' );
		}


}